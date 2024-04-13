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

namespace kaliphp\lib;
use kaliphp\config;
use kaliphp\log;
use kaliphp\util;
use Mosquitto\Client;
use Mosquitto\Message;

/**
 * MQTT操作类 
 * 连接错误也没有异常抛出，不知道为啥
 * MQTT 英文操作文档: https://mosquitto-php.readthedocs.io/en/latest/
 * MQTT 中文操作文档: https://www.kancloud.cn/liao-song/mosquitto-php/500403
 * ex:
   cls_mqtt::instance()->send( 'topic', 'payload', 1 );
 * 
 * @version 1.0.0
 */
class cls_mqtt
{
    public static $config = [];

    /**
     * 实例名 
     * 
     * @var string
     */
    private $name;

    /**
     * MQTT Client类实例
     *
     * @var \Mosquitto\Client
     */
    private $handler;

    /**
     * 连接配置信息 
     * 
     * @var array
     */
    private $connect;

    /**
     * 发布消息成功回调 
     * 
     * @var mixed
     */
    public $on_publish_callback;

    /**
     * 断开链接回调 
     * 
     * @var mixed
     */
    public $on_disconnect_callback;

    /**
     * 实例数组 
     *
     * @var array
     */
    private static $_instances = [];

    /**
     * 链接次数
     *
     * @var int 
     */
    public static $count = 0;

    public static function _init()
    {
        self::$config = config::instance('cache')->get();
    }

    /**
     * @param string $name
     * @return self
     */
    public static function instance( $name = 'kaliphp', array $config = null )
    {
        $name = static::get_muti_name($name);
        log::debug($name);
        if (!isset(self::$_instances[$name]))
        {
            // 没有传配置则调用系统配置好的
            if ( $config === null ) 
            {
                $config = self::$config['mqtt']['server'];
            }
            self::$_instances[$name] = new self($name, $config);
        }
        return self::$_instances[$name];
    }

    /**
     * 构造函数，根据池的初始化检测用户登录信息
     *
     * @param $config   链接配置
     */
    public function __construct( string $name, array $config = null )
    {
        $this->name    = $name;
        $this->connect = $config;
    }

    /**
     * 选择 MQTT 实例
     * @param $name
     * @return cls_redis
     */
    public function choose($name)
    {
        return self::instance(static::get_muti_name($name));
    }

    /**
     * 创建 handler
     */
    private function connect($config = null)
    {
        if ( self::$count != 0 ) 
        {
            log::debug("MQTT reconnect " . self::$count);
        }

        $config = $this->connect;

        $client_id = isset($config['client_id']) ? $config['client_id'] : $this->name;
        $this->handler = new Client($client_id);
        // QoS 1 and 2 消息队列
        $this->handler->setMaxInFlightMessages(100);

        if( !empty($config['user']) && !empty($config['pass']) )
        {
            $this->handler->setCredentials($config['user'], $config['pass']);
        }

        // 证书登录方式
        if (!empty($config['tls-crt'])) 
        {
            $this->handler->setTlsCertificates(
                $config['tls-crt']['ca_file'],
                $config['tls-crt']['cert_file'],
                $config['tls-crt']['key_file'],
                $config['tls-crt']['password']
            );
        }
        else if (!empty($config['tls-psk'])) 
        {
            $this->handler->setTlsPSK(
                $config['tls-psk']['psk'], 
                $config['tls-psk']['identity'], 
                $config['tls-psk']['ciphers']
            );
        }

        // 无法连接会抛出系统级别错误，所以不需要做异常处理
        $this->handler->connect($config['host'], $config['port'], $config['keep-alive']);

        // 只有 loopForever 的时候会触发，loop 多少秒也不能让他触发
        if (is_callable($this->on_publish_callback)) 
        {
            $callback = $this->on_publish_callback;
            $this->handler->onPublish(function($mid) use ($callback) {
                call_user_func($callback, $mid);
            });
        }

        if (is_callable($this->on_disconnect_callback)) 
        {
            $callback = $this->on_disconnect_callback;
            // $rc 为0表示客户端请求断开，其他任何值表示意外断开
            $this->handler->onDisconnect(function($rc) use ($callback) {
                call_user_func($callback, $rc);
            });
        }

        self::$count++;
        return $this;
    }

    public function get_socket()
    {
        if ( !$this->handler ) 
        {
            return -2;
        }

        return $this->handler->getSocket();
    }

    public function on_publish_callback($callback)
    {
        if (!is_callable($callback)) 
        {
            return $this;
        }

        $this->on_publish_callback = $callback;

        return $this;
    }

    public function on_disconnect_callback($callback)
    {
        if (!is_callable($callback)) 
        {
            return $this;
        }

        $this->on_disconnect_callback = $callback;

        return $this;
    }

    /**
     * 消息发布 
     * 
     * @param mixed $topic    消息主题 
     * @param mixed $payload  消息内容 
     * @param int $qos qos    消息回执
     *                           0: 最多一次，不保证消息送达
     *                           1: 最少一次，保证消息送达，需要自己处理重复信息
     *                           2: 只一次，保证只有一条消息送达，不需要自己处理重复信息，性能最差
     * @param mixed $retain   消息是否置顶
     * 
     * @return void
     */
    public function publish($topic, $payload, $qos = 0, $retain = false)
    {
        $try_times = 0;
        //最大尝试获取socket的次数
        while( ++$try_times <= 10 )
        {
            // echo $try_times."\n";
            if ( $this->get_socket() > 0 )
            {
                break;
            }

            $this->connect();
        }

        $ret = $this->handler->publish($topic, $payload, $qos, $retain);
        try
        {
            log::debug(__method__." --- 222 --- ".$this->get_socket());
            // QoS 为1、2的时候，除了消息体还有回执内容，loop一下确保信息能够发完
            $this->handler->loop(100);
        }
        catch (\Mosquitto\Exception $e)
        {
            $msg = $e->getMessage();
            log::debug($msg);

            // 彻底销毁连接
            $this->handler = null;
            // return $this->publish($topic, $payload, $qos, $retain);
        }
        //$this->handler->loopForever();

        return $ret;
    }

    /**
     * cli模式加上进程ID,防止多进程实例串行
     * @param  string $name 实例名称
     * @return string       cli下带进程号的实例名称
     */
    public static function get_muti_name($name = 'mqtt')
    {
        if (PHP_SAPI == 'cli')
        {
            $pid = ':'.posix_getpid();
            if ( strpos($name, $pid) === false ) 
            {
                $name .= $pid;
            }
        }
        
        return $name;
    }

    public function get_class_methods()
    {
        $methods = get_class_methods($this->handler);
        print_r($methods);
        return $methods;
    }

    /**
     * 调用MQTT其他方法
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($this->get_socket() < 0)
        {
            $this->connect();
        }
        return call_user_func_array([$this->handler, $method], $arguments);
    }
} 
