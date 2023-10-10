<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       http://kaliphp.com
 */

namespace kaliphp;
use kaliphp\kali;
use kaliphp\lib\cls_chrome;
use kaliphp\lib\cls_cli;
use kaliphp\lib\cls_benchmark;

defined('SYS_CONSOLE') or define('SYS_CONSOLE', false);

/**
 * 默认日志类
 *
 * 注意：通常情况下，debug产生的非致命错误，不要使用日志系统记录，由debug系统自行控制
 *
 * @since 2011-07-20
 * @version $Id$
 */
class log
{
    /**
     * container for the Monolog instance
     */
    protected static $monolog = null;

    // 定义默认错误级别
    public static $levels = [
        0   => 'NONE',
        99  => 'ALL',
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

    // 日志记录内存变量
    private static $logs = [];

    //最大缓存日志数
    private static $max_log = 128;

    public static function _init()
    {
        if ( config::instance('log')->get('log_type') == 'monolog' && class_exists('\Monolog\Logger') ) 
        {
            static::$monolog = new \Monolog\Logger('kaliphp');
        }
        else 
        {
            config::instance('log')->set('log_type', 'file');
        }

        static::initialize();

        // 程序退出时保存日志
        register_shutdown_function(function () {
            self::save();
        });
    }

    /**
     * 是否运行在控制台
     * 
     * @return void
     */
    public static function is_terminal()
    {
        return defined("STDERR") && is_resource(STDERR) && function_exists('posix_isatty') && posix_isatty(STDERR);
    }

    /**
     * return the monolog instance
     */
    public static function instance()
    {
        return static::$monolog;
    }

    /**
     * initialize the created the monolog instance
     */
    public static function initialize()
    {
        $path = config::instance('log')->get('log_path', APPPATH.DS.'data'.DS.'log'.DS);

        // and make sure it exsts
        if ( ! is_dir($path) or ! is_writable($path))
        {
            config::instance('log')->set('log_threshold', NONE);
            throw new \Exception('Unable to create the log file. The configured log path "'.$path.'" does not exist.');
        }

        //// determine the name of the logfile
        //$filename = config::instance('log')->get('log_file');
        //if (empty($filename))
        //{
            //$filename = date('Y-m-d').'.log';
            ////$filename = date('Y').DS.date('m').DS.date('d').'.log';
        //}

        //$fullpath = dirname($filename);

        //// make sure the log directories exist
        //try
        //{
            //// make sure the full path exists
            //if ( ! is_dir($path.$fullpath))
            //{
                //util::path_exists($path.$fullpath);
            //}

            //// open the file
            //$handle = fopen($path.$filename, 'w');
            //@chmod($path.$filename, 0777);
            //fclose($handle);
        //}
        //catch (\Exception $e)
        //{
            //config::instance('log')->set('log_threshold', NONE);
            //throw new \Exception('Unable to access the log file. Please check the permissions on '.config::instance('log')->get('log_path').'. ('.$e->getMessage().')');
        //}

        if ( config::instance('log')->get('log_type') == 'monolog' ) 
        {
            // create the streamhandler, and activate the handler
            $stream = new \Monolog\Handler\StreamHandler($path.$filename, \Monolog\Logger::DEBUG);
            $formatter = new \Monolog\Formatter\LineFormatter(config::instance('log')->get('log_output'), config::instance('log')->get('log_date_format', 'Y-m-d H:i:s'));
            $stream->setFormatter($formatter);
            static::$monolog->pushHandler($stream);
        }
    }
    /**
     * 计算内存消耗
     *
     * @param $size
     * @return string
     */
    private static function convert($size)
    {
        $unit=['b','kb','mb','gb','tb','pb'];
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * 写入执行信息记录
     */
    public static function exec_log($start = 'total_execution_start', $end = 'total_execution_end', $type = 0)
    {
        if( !config::instance('log')->get('exec_log', false) ) : return false; endif;
        if( req::method() == 'CLI' ) : return false; endif;

        $exe_log['ip']          = req::ip();
        $exe_log['exe_url']     = req::url();
        $exe_log['exe_time']    = cls_benchmark::elapsed_time(  $start, $end);
        $exe_log['exe_memory']  = cls_benchmark::elapsed_memory($start, $end);
        $exe_log['exe_os']      = req::os();
        $exe_log['browser']     = req::browser();
        $exe_log['referrer']    = req::referrer();
        $exe_log['session_id']  = session_id();
        $exe_log['add_time']    = time();
        $exe_log['type']        = $type;
        $exe_log['uid']         = kali::$auth->uid; // CLI 下无法获取，会报异常

        $log_file = APPPATH.DS.'data'.DS.'log'.DS.'exe_log.log';

        //print_r($exe_log);
        //$arr = var_export($exe_log, true);
        $json = json_encode($exe_log, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

        //$json = $json . "==========================================================================";
        file_put_contents($log_file, $json."\n\n", FILE_APPEND | LOCK_EX);
        @chmod($log_file, 0777);
    }

    /**
     * 事件触发写sql
     *
     * @param $e
     * @param $sql
     */
    public function event($e, $sql)
    {
        return static::write(INFO, $sql, $e);
    }

    /**
     * Logs a message with the Error Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function error($msg, $context = null)
    {
        return static::write(ERROR, $msg, $context);
    }

    /**
     * Logs a message with the Warning Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function warning($msg, $context = null)
    {
        return static::write(WARNING, $msg, $context);
    }

    public static function warn($msg, $context = null)
    {
        return static::warning($msg, $context);
    }


    /**
     * Logs a message with the Info Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function info($msg, $context = null)
    {
        return static::write(INFO, $msg, $context);
    }

    /**
     * Logs a message with the Debug Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function notice($msg, $context = null)
    {
        return static::write(NOTICE, $msg, $context);
    }

    /**
     * Logs a message with the Debug Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function debug($msg, $context = null)
    {
        return static::write(DEBUG, $msg, $context);
    }

    // apache log4j
    public static function trace($msg, $context = null)
    {
        return static::write(TRACE, $msg, $context);
    }

    // apache log4j
    public static function fatal($msg, $context = null)
    {
        return static::write(FATAL, $msg, $context);
    }

    /**
     * Logs a message with the Critical Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function critical($msg, $context = null)
    {
        return static::write(CRITICAL, $msg, $context);
    }

    /**
     * Logs a message with the Alert Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function alert($msg, $context = null)
    {
        return static::write(ALERT, $msg, $context);
    }

    /**
     * Logs a message with the Emergency Log Level
     *
     * @param   string  $msg     The log message
     * @param   string  $method  The method that logged
     * @return  bool    If it was successfully logged
     */
    public static function emergency($msg, $context = null)
    {
        return static::write(EMERGENCY, $msg, $context);
    }

    /**
     * Write a log entry to Monolog
     *
     * @param	int|string    $level    the log level
     * @param	string        $msg      the log message
     * @param	array         $context  message context
     * @return	bool
     */
    public static function log($level, $msg, $context = null)
    {
        static::write($level, $msg, $context);
    }

    public static function memory($key="memory")
    {
        return static::write(DEBUG, self::convert(memory_get_usage()), $key);
    }

    public static function time($key="time")
    {
        return static::write(DEBUG, microtime(true), $key);
    }

    /**
     * Write Log File
     *
     * 增加一条日志记录(并不会马上保存，由系统结束运行时调用 log::save 方法保存)
     *
     * @param	int|string	$level		the error level, you can use $path1/$path2/$level
     * @param	string		$msg		the error message
     * @param	string		$method		information about the method
     * @return	bool
     * @throws	Exception
     */
    public static function write($level, $msg, $context = null)
    {
        if ( ! static::need_logging($level))
        {
            return false;
        }

        // 如果是对象，先转化成数组
        if ( is_object($msg) ) 
        {
            $msg = self::object_to_array($msg);
        }

        // 如果是数组，先转化成json
        if ( is_array($msg) ) 
        {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }

        // 如果是数组，先转化成json
        if ( is_array($context) ) 
        {
            $context = json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        $msg = empty($context) ? $msg : $context.' - '.$msg;

        self::$logs[ $level ][] = $msg;
        if( PHP_SAPI == 'cli' || count(self::$logs[ $level ]) >= self::$max_log ) 
        {
            self::save();
            self::$logs[ $level ] = array();
        }
        return true;
    }

    /**
     * 保存日志(由php运行结束时自动调用)
     *
     * @return void
     */               
    public static function save()
    {
        // 保存到日志文件
        foreach( self::$logs as $level => $msgs )
        {
            if ( isset(self::$levels[$level]) ) 
            {
                $is_sys_log = true;
                $level = strtolower(self::$levels[$level]);
            }
            else 
            {
                $is_sys_log = false;
                $level = strtolower($level);
            }

            // 是否输出到浏览器
            if ( SYS_CONSOLE && $is_sys_log ) 
            {
                foreach( $msgs as $msg ) 
                {
                    cls_chrome::$level($msg);
                }
            }

            if ( self::is_terminal() && $is_sys_log ) 
            {
                foreach( $msgs as $msg ) 
                {
                    cls_cli::$level($msg);
                }
            }

            if ( config::instance('log')->get('log_type') == 'monolog' && $is_sys_log ) 
            {
                foreach($msgs as $msg) 
                {
                    static::instance()->log($level, $msg);
                }
            }
            else 
            {
                $log_file = APPPATH.DS.'data'.DS.'log'.DS.$level.'.log';
                $log_msgs = '';
                foreach($msgs as $msg) 
                {
                    $msg  = self::_format_line($level, $msg);
                    $log_msgs .= $msg;
                }

                file_put_contents($log_file, $log_msgs, FILE_APPEND | LOCK_EX);
                @chmod($log_file, 0777);
                self::$logs = array();
            }
        }
    }

    /**
     * 获取实例
     * @param $obj
     * @return array
     */
    private static function object_to_array($obj)
    {
        $arr = [];
        $class = new \ReflectionClass($obj);
        $properties = $class->getProperties();
        foreach ($properties as $propertie){
            $value = $propertie->isPrivate() ? ":private" :
                ($propertie->isProtected() ? ":protected" :
                ($propertie->isPublic() ? ":public" : ""));
            $arr[$propertie->getName()] = $value;
        }
        return [$class->getName() => $arr];
    }

    /**
     * Format the log line.
     *
     * This is for extensibility of log formatting
     * If you want to change the log format, extend the CI_Log class and override this method
     *
     * @param	string	$level 	The error level
     * @param	string	$date 	Formatted date string
     * @param	string	$msg 	The log message
     * @return	string	Formatted log line with a new line character '\n' at the end
     */
    private static function _format_line($level, $msg)
    {
        $level  = strtoupper($level);
        $date   = date(config::instance('log')->get('log_date_format', 'Y-m-d H:i:s'));
        $output = config::instance('log')->get('log_output', "%datetime% [%level_name%] --> %message%\n");
        $msg    = str_replace(['%datetime%', '%level_name%', '%message%'], [$date, $level, $msg], $output);
        return $msg;
    }

    /**
     * 检查日志是否需要记录
     *
     * @param	int|string    $level     日志级别
     * @return	bool
     */
    protected static function need_logging($level)
    {
        $loglabels = config::instance('log')->get('log_threshold');

        // 不记录日志
        if ( $loglabels == NONE ) 
        {
            return false;
        }

        // 记录所有日志
        if ( $loglabels == ALL ) 
        {
            return true;
        }

        // 如果不是数组，采用比他级别高的级别
        if ( ! is_array($loglabels))
        {
            $a = array();
            foreach (self::$levels as $l => $label)
            {
                $l >= $loglabels and $a[] = $l;
            }
            $loglabels = $a;
        }

        if ( is_string($level) )
        {
            if ( ! $level = array_search($level, self::$levels))
            {
                $level = 250;	// 无法映射，转为 NOTICE
            }
        }

        // make sure $level has the correct value
        if ( (is_int($level) and ! isset(self::$levels[$level])) or (is_string($level) and ! array_search(strtoupper($level), self::$levels)) )
        {
            throw new \Exception('Invalid level "'.$level.'" passed to logger()');
        }

        // 是否记录当前日志级别
        if ( ! in_array($level, $loglabels))
        {
            return false;
        }

        return true;
    }
}

/* vim: set expandtab: */

