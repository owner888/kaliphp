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

/**
 * 默认日志类
 *
 * 注意：通常情况下，debug产生的非致命错误，不要使用日志系统记录，由debug系统自行控制
 *
 * @since 2011-07-20
 * @author seatle<seatle@foxmail.com>
 * @version $Id$
 */
class log
{
    public static $config = [];

    // 定义默认错误级别
    public static $levels = array(
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    );

    //日志记录内存变量
    private static $logs = [];

    //终端输出变量
    public static $console_out = [];

    //最大缓存日志数
    private static $max_log = 128;

    private static $_date_fmt = 'Y-m-d H:i:s';

    public static function _init()
    {
        self::$config = config::instance('app_config')->get('log');

        if ( !empty(self::$config['log_date_format'])) 
        {
            self::$_date_fmt = self::$config['log_date_format'];
        }

        // 程序退出时保存日志
        register_shutdown_function(function () {
            //echo __method__."\n";
            self::save();
        });
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
        if (is_object($msg)) 
        {
            $msg = self::object_to_array($msg);
        }

        // 如果是数组，先转化成json
        if (is_array($msg)) 
        {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }

        $msg = empty($context) ? $msg : $context.' - '.$msg;

        $level = strtolower(self::$levels[$level]);

        self::$console_out[$level][] = $msg;

        $date = date(self::$_date_fmt);
        $msg  = self::_format_line($level, $date, $msg);

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
        if (SYS_CONSOLE) 
        {
            foreach (self::$console_out as $level=>$msgs) 
            {
                foreach ($msgs as $msg) 
                {
                    cls_chrome::$level($msg);
                }
            }
        }
        foreach(self::$logs as $log_name => $log_datas )
        {
            $log_file = kali::$log_root.DS.$log_name.'.log';
            $msgs = '';
            foreach($log_datas as $msg) 
            {
                $msgs .= $msg."\n";
            }

            file_put_contents($log_file, $msgs, FILE_APPEND | LOCK_EX);
            @chmod($log_file, 0777);
            self::$logs = array();
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
    private static function _format_line($level, $date, $msg)
    {
        $msg = $date . ' [' . $level .']' . ' --> ' . $msg;
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
        $loglabels = self::$config['log_threshold'];

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

        if (is_string($level))
        {
            if ( ! $level = array_search($level, self::$levels))
            {
                $level = 250;	// 无法映射，转为 NOTICE
            }
        }

        // make sure $level has the correct value
        if ((is_int($level) and ! isset(self::$levels[$level])) or (is_string($level) and ! array_search(strtoupper($level), self::$levels)))
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

