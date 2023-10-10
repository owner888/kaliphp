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
use kaliphp\lib\cls_security;

//event 默认事件
defined('beforeAction') or define('beforeAction', 1);
defined('afterAction') or define('afterAction', 2);
defined('onException') or define('onException', 3);
defined('onError') or define('onError', 4);
defined('onRequest') or define('onRequest', 5);
defined('onResponse') or define('onResponse', 6);
defined('onFilter') or define('onFilter', 7);
defined('onSql') or define('onSql', 'onSql');

/**
 * 事件类 
 *
 * 要触发这里面的方法，要先执行 event::start();
 * 
 * @version 2.7.0
 * @copyright 1997-2019 The PHP Group
 * @created time :2019-05-16
 */
class event
{
    public static $config = [];

    /**
     * 事件句柄
     * @var int
     */
    private static $fh = 0;

    /**
     * 事件观察者
     * @var array
     */
    private static $monitors = [];

    public static function _init()
    {
        self::$config = config::instance('log')->get();
    }

    /**
     * 绑定事件
     * @param callable $method
     * @param $event
     * @param null $times
     * @return int
     * @throws TXException
     */
    public static function bind($method, $event, $times=null)
    {
        if (!is_callable($method))
        {
            throw new \Exception(isset($method[1]) ? $method[1] : 'null', 5003);
        }
        $fh = ++self::$fh;
        self::$monitors[$event][$fh] = ['m'=>$method, 't'=>$times];
        return $fh;
    }

    
    /**
     * 将配置文件的hook绑定
     * @param $confHooks
     * @notice 要求绑定的回调的类必须是 app.hook下或bingo.hook下的类，
     *         原类名必须以Hook结尾，但是写在配置文件时不用写结尾的Hook,
     *         例如：['test'=>[['test','method']]]里的绑定方法会被处理为：
     *         TestHook::method
     * @notice 类名不可以是句点格式，默认会先从app的hook目录查找，
     *         再去bingo的hook目录查找
     */
    public static function bind_by_config($events)
    {
        foreach ($events as $event_name => $callbacks)
        {
            foreach ($callbacks as $callback) 
            {
                if (is_array($callback) and $callback)
                {

                    $callback[0] = ucfirst($callback[0]).'Hook';
                }
                else if (is_null($callBack)) 
                {
                    self::$_hooks[$hookName] = array();
                }
                self::bindHook($hookName,$callBack);
            }
        }
    }

    /**
     * 绑定永久事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function on($event, $method=null)
    {
        $method = $method ?: ['\kaliphp\log', 'debug'];
        return self::bind($method, $event);
    }

    /**
     * 绑定一次事件
     * @param callable $method
     * @param $event
     * @return int
     */
    public static function one($event, $method=null)
    {
        $method = $method ?: ['\kaliphp\log', 'debug'];
        return self::bind($method, $event, 1);
    }

    /**
     * 解绑事件
     * @param $event
     * @param $fh
     * @return bool
     */
    public static function off($event, $fh=null)
    {
        if ($fh)
        {
            if (isset(self::$monitors[$event][$fh]))
            {
                unset(self::$monitors[$event][$fh]);
                return true;
            } 
            else 
            {
                return false;
            }
        } 
        else 
        {
            unset(self::$monitors[$event]);
            return true;
        }
    }

    /**
     * 触发事件
     * @param $event
     * @param array $params
     * @return bool
     */
    public static function trigger( $event, $params = [] )
    {
        if (!isset(self::$monitors[$event]))
        {
            return false;
        }
        // 把 $event 插入 $params 数组中
        array_unshift($params, $event);
        foreach (self::$monitors[$event] as $fh => &$value)
        {
            $method = $value['m'];
            call_user_func_array($method, $params);
            if (isset($value['t']) && --$value['t'] <= 0)
            {
                unset(self::$monitors[$event][$fh]);
            }
        }
        unset($value);
        return true;
    }

    /**
     * 启动类
     */
    public static function start()
    {
        event::on(onException, ['kaliphp\event', 'on_exception']);
        event::on(onFilter, ['kaliphp\event', 'on_filter']);
        event::on(onRequest, ['kaliphp\event', 'on_request']);
        event::on(onResponse, ['kaliphp\event', 'on_response']);
        event::on(onSql, ['kaliphp\event', 'on_sql']);
    }

    /**
     * 异常错误
     * @param $event
     * @param $code
     * @param $params
     */
    public static function on_exception($event, $code, $params)
    {
        log::error("ERROR CODE: $code\n".join("\n", $params));
    }

    /**
     * 请求入口
     * @param $event
     * @param $request TXRequest
     */
    public static function on_filter($event)
    {
        // IP过滤器
        cls_security::ip_filter();
        // 国家过滤器
        cls_security::country_filter();

        //log::info('filter: '.$request->getHostInfo().$request->getUrl());
    }

    /**
     * 请求
     * @param $event
     */
    public static function on_request($event)
    {
        if ( req::method() != 'CLI' ) 
        {
            // 是否允许跨域
            $allow_origin = config::instance('config')->get('security.allow_origin');
            $origin = req::server('HTTP_ORIGIN');
            if ( $allow_origin != NULL && (in_array('*', $allow_origin) || in_array($origin, $allow_origin)) ) 
            {
                header("Access-Control-Allow-Origin: {$origin}");
                header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, OPTIONS");
                header("Access-Control-Allow-Credentials: true");
            }
        }

        // 不在日志记录方法里面
        if ( !in_array(req::method(), self::$config['log_request_methods']) && !in_array('*', self::$config['log_request_methods'])) 
        {
            return;
        }

        $url = sprintf("ct=%s&ac=%s", kali::$ct, kali::$ac);
        // 不是日志记录请求
        if ( !in_array($url, self::$config['log_request_uris']) && !in_array('*', self::$config['log_request_uris']) ) 
        {
            return;
        }

        $forms = [
            'ct' => 'index',
            'ac' => 'index',
        ];
        $forms = array_merge($forms, req::$forms);
        log::info('request: '.json_encode($forms), req::method());
        //log::info('request: '.$request->getHostInfo().$request->getUrl());
    }

    /**
     * 响应
     * @param $event
     */
    public static function on_response($event)
    {
        $url = sprintf("ct=%s&ac=%s", kali::$ct, kali::$ac);
        // 不在日志记录方法里面
        if ( !in_array(req::method(), self::$config['log_request_methods']) && !in_array('*', self::$config['log_request_methods'])) 
        {
            return;
        }

        // 不是日志记录请求
        if ( !in_array($url, self::$config['log_request_uris']) && !in_array('*', self::$config['log_request_uris']) ) 
        {
            return;
        }

        $forms = [
            'ct' => 'index',
            'ac' => 'index',
        ];
        $forms = array_merge($forms, req::$forms);
        log::info('response: '.json_encode($forms));
        //log::info('request: '.$request->getHostInfo().$request->getUrl());
    }

    public static function on_sql($event, string $sql = null, string $db_name = null, $query_time = 0)
    {
        log::info(sprintf('SQL Query [%s]: %s (%ss)', $db_name, $sql, $query_time));

        if ( SYS_CONSOLE )
        {
            cls_chrome::info('%c SQL语法 %c '.$sql.' ','color: white; background: #34495e; padding:5px 0;', 'background: #95a5a6; padding:5px 0;');
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $step = 1;
            $filter_arr = ['kaliphp\event::trigger','kali::run'];
            foreach ($backtrace as $val)
            {
                if(!isset($val['class']) || !isset($val['line'])) continue;
                if (strpos($val['file'],'kali.php') !== false) continue;
                $full_method = $val['class'] . $val['type'] . $val['function'];
                if (in_array($full_method,$filter_arr)) continue;
                if ($full_method !== 'kaliphp\database\db_connection->execute')
                {
                    cls_chrome::info('%c 第 ' . $step . ' 层 %c ' .$val['class'] . $val['type'] . $val['function'] . ' ','color: white; background: #d35400; padding:5px 0;', 'background: #e67e22; padding:5px 0;');
                    ++ $step;
                }
                cls_chrome::info('%c 路径 %c ' .str_replace(array('%file','%line'), array($val['file'],$val['line']),SYS_EDITOR) . ' ','color: white; background: #27ae60; padding:5px 0;', 'background: #2ecc71; padding:5px 0;');
            }
        }
    }
}
