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

namespace kaliphp\lib;
use kaliphp\config;

/**
 * Redis操作类 
 * 
 * @version 2.7.0
 */
class cls_redis
{
    public static $config = [];

    /**
     * @var \Redis
     */
    private $handler;
    private $connect;

    private static $_instances = [];

    public static function _init()
    {
        self::$config = config::instance('cache')->get();
    }

    /**
     * @param string $name
     * @return TXRedis
     */
    public static function instance( $name='redis', array $config = null )
    {
        if (!isset(self::$_instances[$name]))
        {
            // 没有传配置则调用系统配置好的
            if ( $config === null ) 
            {
                $config = self::$config['redis']['server'];
            }
            self::$_instances[$name] = new self($config);
        }
        return self::$_instances[$name];
    }

    /**
     * 构造函数，根据池的初始化检测用户登录信息
     *
     * @param $config   链接配置
     */
    public function __construct( array $config = null )
    {
        $this->connect = $config;
    }

    /**
     * 选择 Redis 实例
     * @param $name
     * @return cls_redis
     */
    public function choose($name)
    {
        return self::instance($name);
    }

    /**
     * 创建handler
     * @throws TXException
     */
    private function connect($config = null)
    {
        $config = $this->connect;

        $this->handler = new \Redis();
        if (isset($config['keep-alive']) && $config['keep-alive'])
        {
            $this->handler->pconnect($config['host'], $config['port'], 60);
        } 
        else 
        {
            $this->handler->connect($config['host'], $config['port']);
        }
        if($config["pass"])
        {
            $this->handler->auth($config["pass"]);
        }
        if( $config['dbindex'] )
        {
            $this->handler->select($config['dbindex']);
        }
        // 不需要了，连不上Redis自己会throw
        //throw new \Exception(serialize([$config['host'], $config['port']]), 4005);

        // 不序列化的话不能存数组，用php的序列化方式其他语言又不能读取，所以这里自己用json序列化了，性能还比php的序列化好1.4倍
        //$this->handler->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);         // don't serialize data
        //$this->handler->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);          // use built-in serialize/unserialize
        //$this->handler->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);     // use igBinary serialize/unserialize
        //$this->handler->setOption(Redis::OPT_PREFIX, $configs['prefix'] . ":");           // 设置前缀
    }

    /**
     * set
     * 
     * @param string        $key    键
     * @param string|array  $value  值
     * @param int           $expire 过期时间，单位：秒，小于等于0则设置一个永不过期的值
     * @return bool
     */
    public function set( $key, $value, $expire = 0, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        $value = $serialize ? $this->encode($value) : $value;

        if ( $expire > 0 ) 
        {
            return $this->handler->setex($key, $expire, $value);
        }
        else 
        {
            return $this->handler->set($key, $value);
        }
    }

    public function get( $key, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        return $serialize ? $this->decode($this->handler->get($key)) : $this->handler->get($key);
    }

    public function hget( $key, $hash, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        return $serialize ? $this->decode($this->handler->hGet($key, $hash)) : $this->handler->hGet($key, $hash);
    }

    public function hset( $key, $hash, $value, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        $value = $serialize ? $this->encode($value) : $value;
        return $this->handler->hSet($key, $hash, $value);
    }

    public function lpush( $key, $value, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        $value = $serialize ? $this->encode($value) : $value;
        return $this->handler->lpush($key, $value);
    }

    public function rpop( $key, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        return $serialize ? $this->decode($this->handler->rpop($key)) : $this->handler->rpop($key);
    }

    public function rpush( $key, $value, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        $value = $serialize ? $this->encode($value) : $value;
        return $this->handler->rpush($key, $value);
    }

    public function lpop( $key, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        return $serialize ? $this->decode($this->handler->lpop($key)) : $this->handler->lpop($key);
    }

    public function lindex( $key, $index, $serialize = null )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        if ($serialize === null)
        {
            $serialize = self::$config['serialize'];
        }
        return $serialize ? $this->decode($this->handler->lindex($key, $index)) : $this->handler->lindex($key, $index);
    }

    public function encode($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function decode($value)
    {
        return json_decode($value, true);
    }

    /**
     * 调用redis其他方法
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!$this->handler)
        {
            $this->connect();
        }
        return call_user_func_array([$this->handler, $method], $arguments);
    }

}


