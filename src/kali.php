<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       https://doc.kaliphp.com
 */

namespace kaliphp;

use kaliphp\req;
use kaliphp\cache;
use kaliphp\event;
use kaliphp\config;
use kaliphp\session;
use kaliphp\lib\cls_benchmark;
use kaliphp\lib\cls_security;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 严格开发模式
error_reporting( E_ALL );
ini_set('display_errors', 'On');

//if( DEBUG_MODE === true || $_debug_safe_ip )
//{
    //ini_set('display_errors', 'On');
//}
//else
//{
    //ini_set('display_errors', 'Off');
//}

/**
 * The core of the framework.
 *
 * @package		Kali
 * @subpackage	Core
 */
class kali
{
    public static $config = [];
    public static $base_root;
    public static $data_root;
    public static $cache_root;
    public static $log_root;

    /**
     * 权限类的实例
     *
     * @var $auth cls_auth 用于IDE跳转代码
     */
    public static $auth = null;

    // 当前 Controller 和 Action
    public static $ct = '';
    public static $ac = '';

    /**
     * Initializes the framework.  This can only be called once.
     *
     * @access	public
     * @return	void
     */
    public static function registry(?array $config = [])
    {
        // 获取配置
        self::$config = $config;

        defined('ENVPATH') or define('ENVPATH', '.env');

        if (file_exists(ENVPATH) && ($envs = parse_ini_file(ENVPATH))) 
        {
            foreach ($envs as $k => $v) 
            {
                if (strpos($k, "# ") === false) // 过滤注释的行
                {
                    $_ENV[$k] = $v;
                }
            }
        }

        if ( !defined('APPPATH'))
        {
            exit(self::fmt_code(1006, ['APPPATH']));
        }    

        self::$base_root    = APPPATH;
        self::$data_root    = self::$base_root.DS."data";
        self::$log_root     = self::$base_root.DS."data".DS."log";
        self::$cache_root   = self::$base_root.DS."data".DS."cache";

        if ( !is_readable(self::$base_root) ) 
        {
            exit(self::fmt_code(1001, [self::$base_root]));
        }

        if ( !is_writable(self::$log_root) && !@mkdir(self::$log_root) )
        {
            exit(self::fmt_code(1007, [self::$log_root]));
        }

        if ( !is_writable(self::$cache_root) && !@mkdir(self::$cache_root) )
        {
            exit(self::fmt_code(1007, [self::$cache_root]));
        }

        // 设置一下路径，让 use 类生效
        autoloader::set_root_path(APPPATH);

        self::define();

        if ( PHP_SAPI != 'cli' && !empty(self::$config['session_start']) ) 
        {
            $token = $_SERVER['HTTP_TOKEN'] ?? $_REQUEST['token'] ?? '';
            $token && session_id($token);
            // SESSION 接管
            session::handle();
            session_start();
        }

        self::init();
    }

    /**
     * 初始化定义
     */
    private static function define()
    {
        $http_encrypt = $_SERVER['HTTP_ENCRYPT'] ?? '0';
        // 调用 req 之前处理下 use_encrypt
        if ( PHP_SAPI != 'cli' && $http_encrypt == '1' ) 
        {
            defined('REQUEST_ENCRYPT') or define('REQUEST_ENCRYPT', true);
        }
        defined('REQUEST_ENCRYPT') or define('REQUEST_ENCRYPT', false);

        // mvim://open?url=file://%file&line=%line
        // subl://open?url=file://%file&line=%line
        // idea://open?file=%file&line=%line
        defined('SYS_EDITOR')  or define('SYS_EDITOR', 'mvim://open?url=file://%file&line=%line');
        // 是否打开调试功能
        defined('SYS_DEBUG')   or define('SYS_DEBUG',  (bool) ($_ENV['APP_DEBUG'] ?? false));
        // 打印 Chrome console 日志，需要安装 Chrome Logger 插件: https://chrome.google.com/webstore/detail/chrome-logger/noaneddfkdjfnfdakjjmocngnfkfehhd
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', (bool) ($_ENV['APP_CONSOLE'] ?? false));
        // 系统环境
        defined('SYS_ENV') or define('SYS_ENV', $_ENV['APP_ENV'] ?? 'pub');
        defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
        defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
        defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');

        defined('DEBUG')   or define('DEBUG',   100);
        defined('INFO')    or define('INFO',    200);
        defined('NOTICE')  or define('NOTICE',  250);
        defined('WARNING') or define('WARNING', 300);
        defined('ERROR')   or define('ERROR',   400);

        // Get the start time and memory for use later
        defined('KALI_START_TIME') or define('KALI_START_TIME', microtime(true));
        defined('KALI_START_MEM')  or define('KALI_START_MEM',  memory_get_usage());
        defined('KALI_TIMESTAMP')  or define('KALI_TIMESTAMP',  time());

        // Event default action
        defined('beforeAction') or define('beforeAction', 1);
        defined('afterAction')  or define('afterAction', 2);
        defined('onException')  or define('onException', 3);
        defined('onError')      or define('onError', 4);
        defined('onRequest')    or define('onRequest', 5);
        defined('onResponse')   or define('onResponse', 6);
        defined('onFilter')     or define('onFilter', 7);
        defined('onSql')        or define('onSql', 'onSql');
    }

    /**
     * 核心初始化
     */
    private static function init()
    {
        $timezone_set = config::instance('config')->get('timezone_set');
        date_default_timezone_set($timezone_set);

        register_shutdown_function(['kaliphp\errorhandler', 'shutdown_handler']);
        set_error_handler(['kaliphp\errorhandler', 'error_handler'], E_ALL);
        set_exception_handler(['kaliphp\errorhandler', 'exception_handler']);

        event::start();

        if ( PHP_SAPI != 'cli' )
        {
            defined('IP')      or define('IP', req::ip());
            defined('LANG')    or define('LANG', req::language());
            defined('COUNTRY') or define('COUNTRY', req::country());

            // 触发过滤事件，可对IP，访问国家进行过滤处理
            event::trigger(onFilter);
        }

        // 启动计时器。。。嘀嗒嘀嗒。。。
        cls_benchmark::mark('total_execution_start');
        cls_benchmark::mark('loading_time:_base_classes_start');
    }

    /**
     * 路由映射
     * @param $ctl  控制器
     * @parem $ac   动作
     * @return void
     */
    public static function run(?array $req_data = null)
    {
        // 存在需要转化为路由的数据
        if ( $req_data ) 
        {
            // Websocket 方式，Workerman、Swoole 环境
            if ( PHP_SAPI == 'cli' ) 
            {
                // 清空上一个请求数据，避免数据污染
                req::$forms = req::$gets = [];
            }
            // 把指定数据转化为路由数据
            req::assign_values($req_data);
        }

        // 获取当前控制器及action
        $ct = self::$ct = preg_replace("/[^0-9a-z_]/i", '', req::item('ct', 'index') );
        $ac = self::$ac = preg_replace("/[^0-9a-z_]/i", '', req::item('ac', 'index') );

        // 触发请求事件
        event::trigger(onRequest);

        // 检查权限，Workerman环境先自己检查，后面再实现 
        if( PHP_SAPI != 'cli' && isset(self::$config['check_purview_handle']) )
        {
            kali::$auth = call_user_func_array(self::$config['check_purview_handle'], [$ct, $ac]);
        }

        $ctl  = 'ctl_'.$ct;
        //禁止 _ 开头的方法
        if( $ac[0]=='_' )
        {
            if ( PHP_SAPI != 'cli' ) 
            {
                throw new \Exception('', 2004);
            } 
            else 
            {
                log::warning(errorhandler::fmt_code(2004));
                return false;
            }
        }

        // 验证token，Websocket 方式，Workerman、Swoole 环境排除
        if ( PHP_SAPI != 'cli' ) 
        {
            cls_security::csrf_verify();
        }

        event::trigger(beforeAction);
        cls_benchmark::mark('controller_execution_( '.$ct.' / '.$ac.' )_start');

        $controller = "control\\".$ctl;

        if ( !class_exists($controller) )
        {
            if ( PHP_SAPI != 'cli' ) 
            {
                throw new \Exception($ctl, 2001);
            } 
            else 
            {
                log::warning(errorhandler::fmt_code(2001, $ctl));
                return false;
            }
        } 

        $instance = new $controller();

        if ( !method_exists($instance, $ac) )
        {
            if ( PHP_SAPI != 'cli' ) 
            {
                throw new \Exception(serialize([$ac, $ctl]), 2002);
            } 
            else 
            {
                log::warning(errorhandler::fmt_code(2002, serialize([$ac, $ctl])));
                return false;
            }
        }    

        $instance->$ac();

        cls_benchmark::mark('controller_execution_( '.$ct.' / '.$ac.' )_end');
        // 记录执行日志，config/log.php 文件可以配置是否开启
        log::exec_log( 'total_execution_start', 'total_execution_end');
        event::trigger(afterAction);
    }

    public static function crond()
    {
        $index_time_start = microtime(true);

        $config = config::instance('crond')->get();

        // 提取要执行的文件
        $exe_file = array();
        foreach ($config['the_format'] as $format)
        {
            $key = date($format, ceil($index_time_start));
            if (is_array(@$config['the_time'][$key]))
            {
                $cache_key = $key;
                $exe_file = array_merge($exe_file, $config['the_time'][$key]);
            }
        }

        echo "\n" . date('Y-m-d H:i', time()), "\n\n";
        $size = memory_get_usage();
        $unit = ['b','kb','mb','gb','tb','pb']; 
        $memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
        echo "Start in $memory\n\n\n";

        // 加载要执行的文件
        foreach ($exe_file as $file)
        {
            // 过滤掉不是 core/crond/ 目录下的文件，否则被上传到data目录的php就很危险了
            $pathinfo = pathinfo($file);
            if (empty($pathinfo['basename'])) 
            {
                continue;
            }

            $file = $pathinfo['basename'];
            $path_file = self::$base_root.DS.'crond'.DS.$file;
            echo '  ', $file,"\n";   

            $cache_key = md5($file);
            $keys = [
                'crond_lasttime' => 'crond_lasttime_'.$cache_key,
                'crond_runtime'  => 'crond_runtime_'.$cache_key,
            ];

            $cache_time = 86400;
            $runtime_start = microtime(true);
            if( function_exists('pcntl_fork') )//支持多进程优先使用，防止某个crond中断导致其他的无法执行
            {
                $pid = pcntl_fork();    //创建子进程
                if( $pid == -1 ) //错误处理：创建子进程失败时返回-1.
                {
                    die('Could not fork');
                } 
                else if( $pid ) //父进程会得到子进程号，所以这里是父进程执行的逻辑
                {
                    //如果不需要阻塞进程，而又想得到子进程的退出状态，则可以注释掉pcntl_wait($status)语句，或写成：
                    pcntl_wait($status, WNOHANG); //等待子进程中断，防止子进程成为僵尸进程。
                } 
                else //执行子进程逻辑
                {
                    include $path_file;
                    $lasttime = ceil($runtime_start);
                    $runtime = number_format(microtime(true) - $runtime_start, 3);

                    cache::set($keys['crond_lasttime'], $lasttime, $cache_time);
                    cache::set($keys['crond_runtime'],  $runtime,  $cache_time);
                    // 这里用0表示子进程正常退出
                    exit(0);
                }
            }
            else
            {
                include $path_file;
                $lasttime = ceil($runtime_start);
                $runtime = number_format(microtime(true) - $runtime_start, 3);

                cache::set($keys['crond_lasttime'], $lasttime, $cache_time);
                cache::set($keys['crond_runtime'],  $runtime,  $cache_time);
            }

            echo "\n\n";
        }

        $size = memory_get_usage();
        $unit = ['b','kb','mb','gb','tb','pb']; 
        $memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
        $time = microtime(true) - $index_time_start;
        echo "All done in $time seconds\t $memory\n";
    }

    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    public static function fmt_code($code, $params=[])
    {
        $msgtpl = config::instance('exception')->get($code);
        return vsprintf($msgtpl, $params);
    }

    /**
     * APP统计
     *
     * @return array()
     */
    public static function app_total()
    {
        return [
            microtime(true) - KALI_START_TIME,
            memory_get_peak_usage() - KALI_START_MEM,
        ];
    }
}

/* vim: set expandtab: */

