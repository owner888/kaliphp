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

use kaliphp\config;
use kaliphp\req;
use kaliphp\session;
use kaliphp\cache;
//use kaliphp\router;
//use kaliphp\socket;
//use kaliphp\log;
use kaliphp\event;
use kaliphp\lib\cls_benchmark;
use kaliphp\lib\cls_security;
use kaliphp\lib\cls_auth;
use kaliphp\lib\cls_msgbox;

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

# 基本加载
//include __DIR__.'/config.php';
//include __DIR__.'/event.php';
//include __DIR__.'/log.php';

/**
 * The core of the framework.
 *
 * @package		Kali
 * @subpackage	Core
 */
class kali
{
    // app配置数组
    public static $app_config = [];

    // app标题(一些自动化程序默认会把这个作为title)
    public static $app_title  = '';

    // app名称(必须为无空格英文，模板默认目录)
    public static $app_name   = 'app';

    public static $base_root;
    public static $app_root;
    public static $view_root;
    public static $data_root;
    public static $cache_root;
    public static $log_root;
    public static $extends_root;

    // 是否启动session
    public static $session_start = false;
    // 是否设置session过期时间
    public static $session_expire = false;

    /**
     * 权限类的实例
     *
     * @var $auth cls_auth
     */
    public static $auth = null;

    /**
     * 用户类的实例
     *
     * @var $auth cls_user
     */
    //public static $user = null;

    // 是否ajax/app请求
    public static $is_ajax = false;

    // 当前ct和ac
    public static $ct = '';
    public static $ac = '';

	/**
	 * Initializes the framework.  This can only be called once.
	 *
	 * @access	public
	 * @return	void
	 */
	public static function registry(array $config = null)
	{
        self::define();

        // 获取配置
        self::$app_config = $config;

        if( isset($config['app_title']) ) 
        {
            self::$app_title = $config['app_title'];
        }
        if( isset($config['app_name']) ) 
        {
            self::$app_name = $config['app_name'];
        }
        if( isset($config['session_start']) && $config['session_start'] ) 
        {
            self::$session_start = true;
        }

        self::$base_root    = dirname(__DIR__);
        self::$extends_root = self::$base_root.DS."extends";
        self::$data_root    = self::$base_root.DS."data";
        self::$log_root     = self::$base_root.DS."data".DS."log";
        self::$cache_root   = self::$base_root.DS."data".DS."cache";
        self::$app_root     = self::$base_root.DS."..".DS.self::$app_name;    

        if ( !is_readable(self::$app_root) ) 
        {
            exit(self::fmt_code(1001, [self::$app_root]));
        }

        self::$view_root = self::$app_root.DS."template";

        if ( !is_writable(self::$log_root) && !@mkdir(self::$log_root) )
        {
            exit(self::fmt_code(1007, [self::$log_root]));
        }
        if ( !is_writable(self::$cache_root) && !@mkdir(self::$cache_root) )
        {
            exit(self::fmt_code(1007, [self::$cache_root]));
        }

        self::init();
    }

    /**
     * 初始化定义
     */
    private static function define()
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        // mvim://open?url=file://%file&line=%line
        // subl://open?url=file://%file&line=%line
        // idea://open?file=%file&line=%line
        defined('SYS_EDITOR') or define('SYS_EDITOR', 'mvim://open?url=file://%file&line=%line');

        //定义保护
        defined('RUN_SHELL') or define('RUN_SHELL', false);
        defined('SYS_DEBUG') or define('SYS_DEBUG', false);
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', false);

        defined('SYS_ENV') or define('SYS_ENV', 'pub');
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

        //event 默认事件
        defined('beforeAction') or define('beforeAction', 1);
        defined('afterAction') or define('afterAction', 2);
        defined('onException') or define('onException', 3);
        defined('onError') or define('onError', 4);
        defined('onRequest') or define('onRequest', 5);
        defined('onSql') or define('onSql', 'onSql');
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

        if ( PHP_SAPI != 'cli' ) 
        {
            // 客户端IP
            defined('IP') or define('IP', req::ip());
            // 客户端语言
            defined('LANG') or define('LANG', req::language());
            // 客户端国家
            defined('COUNTRY') or define('COUNTRY', req::country());

            // 触发过滤事件，可对IP，访问国家进行过滤处理
            event::trigger(onFilter);

            if ( self::$session_start ) 
            {
                // session接管
                session::handle();
                session_start();
            }
        }

        // 启动计时器。。。嘀嗒嘀嗒。。。
        cls_benchmark::mark('total_execution_time_start');
        cls_benchmark::mark('loading_time:_base_classes_start');

        event::start();
    }

    /**
     * 路由映射
     * @param $ctl  控制器
     * @parem $ac   动作
     * @return void
     */
    public static function run()
    {
        // 获取当前控制器及action
        self::_get_action();

        // 触发请求事件
        event::trigger(onRequest);

        $ct = self::$ct;
        $ac = self::$ac;

        // 初始化权限类
        if( isset(self::$app_config['purview_config']) )
        {
            $purview_config = self::$app_config['purview_config'];

            // 自动检查权限 
            if( $purview_config['auto_check'] )
            {
                //自定义验证 
                if( isset($purview_config['auto_check_handle']) && is_callable($purview_config['auto_check_handle']) )
                {
                    call_user_func_array($purview_config['auto_check_handle'], [$ct, $ac]);
                }
                else //默认验证
                {
                    $uid = isset($_SESSION[cls_auth::$auth_hand.'_uid']) 
                        ? $_SESSION[cls_auth::$auth_hand.'_uid'] 
                        : 0;
                    self::$auth = cls_auth::instance( $uid );
                    self::$auth->check_purview($ct, $ac, 1);
                    self::$auth->user = self::$auth->get_user();
                    // 用户可自定义session过期时间
                    kali::$session_expire = self::$auth->user['session_expire'];    

                    $safe_actions = ['logout', 'login', 'authentication'];
                    if ( !in_array($ac, $safe_actions) ) 
                    {
                        if( self::$auth->user['lastip'] != IP ) //换了IP,强制重新登陆
                        {
                            self::$auth->logout();
                        }
                        else if(  //登陆IP不在白名单，禁止操作
                            !empty(self::$auth->user['safe_ips']) && 
                            !in_array(IP, explode(',', str_replace('，', ',', self::$auth->user['safe_ips']))) 
                        ) 
                        {
                            $msg = "IP不在白名单内,无法操作";
                            if ( kali::$is_ajax ) 
                            {
                                util::return_json(array(
                                    'code' => -10100,
                                    'msg'  => $msg
                                ));
                            }
                            else 
                            {
                                cls_msgbox::show('用户权限限制', $msg, '');
                            }
                        }
                    }
                }
            }
        }

        $ctl  = 'ctl_'.$ct;

        //禁止 _ 开头的方法
        if( $ac[0]=='_' )
        {
            throw new Exception(serialize([$ac, $ctl]), 2002);
        }

        // 验证token
        cls_security::csrf_verify();

        event::trigger(beforeAction);
        cls_benchmark::mark('controller_execution_time_( '.$ct.' / '.$ac.' )_start');
        $controller = self::$app_name."\\control\\".$ctl;
        $instance = new $controller();
        $instance->$ac();
        cls_benchmark::mark('controller_execution_time_( '.$ct.' / '.$ac.' )_end');
        event::trigger(afterAction);
    }

    public static function crond()
    {
        $index_time_start = microtime(true);

        $config = config::instance('config')->get('crond_timer', 'crond');

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
        $unit = array('b','kb','mb','gb','tb','pb'); 
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

                    cache::set($keys['crond_lasttime'], $lasttime);
                    cache::set($keys['crond_runtime'], $runtime);
                    // 这里用0表示子进程正常退出
                    exit(0);
                }
            }
            else
            {
                include $path_file;
                $lasttime = ceil($runtime_start);
                $runtime = number_format(microtime(true) - $runtime_start, 3);

                cache::set($keys['crond_lasttime'], $lasttime);
                cache::set($keys['crond_runtime'], $runtime);
            }

            echo "\n\n";
        }

        $size = memory_get_usage();
        $unit = array('b','kb','mb','gb','tb','pb'); 
        $memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
        $time = microtime(true) - $index_time_start;
        echo "All done in $time seconds\t $memory\n";
    }

    /**
     * 获取ac和ct
     */
    private static function _get_action()
    {
        self::$ct = preg_replace("/[^0-9a-z_]/i", '', req::item('ct') );
        self::$ac = preg_replace("/[^0-9a-z_]/i", '', req::item('ac') );
        if( self::$ct=='' ) self::$ct = 'index';
        if( self::$ac=='' ) self::$ac = 'index';
    }

    
    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    public static function fmt_code($code, $params=[])
    {
        $msgtpl = config::instance('config')->get($code, 'exception');
        return vsprintf($msgtpl, $params);
    }

    /**
     * APP统计
     *
     * @return array()
     */
    public static function app_total()
    {
        return array(
            microtime(true) - KALI_START_TIME,
            memory_get_peak_usage() - KALI_START_MEM,
        );
    }

}


/* vim: set expandtab: */

