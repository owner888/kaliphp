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
     * @return cla_redis
     */
    public static function instance( $name = 'redis', array $config = null )
    {
        $name = static::get_muti_name($name);
        if (!isset(self::$_instances[$name]))
        {
            // 没有传配置则调用系统配置好的
            if ( $config === null ) 
            {
                $config = self::$config['redis']['server'];
                // 如果把redis当cache用，增加一个 :cache 字符用于 Redis UI 分文件夹查看
                $config['prefix'] = ($name == 'cache') ? self::$config['prefix'].':cache' : self::$config['prefix'];
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
        return self::instance(static::get_muti_name($name));
    }

    /**
     * 创建handler
     * @throws TXException
     */
    private function connect($config = null)
    {
        $config = $this->connect;
        if( class_exists('RedisCluster') && !empty($config['cluster']) )
        {
            $pass = empty($config['cluster']["pass"]) ? null : $config['cluster']["pass"];
            $this->handler = new \RedisCluster(
                null,
                $config['cluster']['host'],
                5, 5, true, $pass
            );
            
            $this->handler->setOption(\RedisCluster::OPT_SCAN, \RedisCluster::SCAN_RETRY);
            //因为集群可能多个项目使用，而集群不支持分库，所以设置一个前缀，所有操作都是隐式加上去的
            if( $config['prefix'] )
            {
                $this->handler->setOption(\RedisCluster::OPT_PREFIX, $config['prefix'] . ':');
            }

            if( defined('\RedisCluster::SERIALIZER_JSON') )
            {
                $this->handler->setOption(\RedisCluster::OPT_SERIALIZER, \RedisCluster::SERIALIZER_JSON);
                $this->connect['serializer'] = 'json';
            }

            $this->connect['is_cluster'] = true;
        }
        else
        {
            $this->handler = new \Redis();
            if ( isset($config['keep-alive']) && $config['keep-alive'] )
            {
                $this->handler->pconnect($config['host'], $config['port'], $config['timeout']);
            } 
            else 
            {
                $this->handler->connect($config['host'], $config['port'], $config['timeout']);
            }

            if( $config["pass"] )
            {
                $this->handler->auth($config["pass"]);
            }

            if( $config['dbindex'] )
            {
                $this->handler->select($config['dbindex']);
            }
            
            if( $config['prefix'] ) 
            {
                $this->handler->setOption(\Redis::OPT_PREFIX, $config['prefix'] . ":");
            }

            if( defined('\Redis::SERIALIZER_JSON') )
            {
                $this->handler->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_JSON);
                $this->connect['serializer'] = 'json';
            }
     
            // 不需要了，连不上Redis自己会throw
            //throw new \Exception(serialize([$config['host'], $config['port']]), 4005);
            // 不序列化的话不能存数组，用php的序列化方式其他语言又不能读取，所以这里自己用json序列化了，性能还比php的序列化好1.4倍
            //$this->handler->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);         // don't serialize data
            //$this->handler->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);          // use built-in serialize/unserialize
            //$this->handler->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);     // use igBinary serialize/unserialize
        }
     
        return $this;
    }

    /**
     * set
     * 
     * @param string        $key    键
     * @param string|array  $value  值
     * @param int           $expire 过期时间，单位：秒，小于等于0则设置一个永不过期的值
     * @return bool
     */
    public function set( $key, $value, $expire = 0 )
    {
       if ( empty($value) ) 
       {
           trigger_error('Cache value cannot be empty');
           return false;
       }

        if (!$this->handler)
        {
            $this->connect();
        }
 
        $value = $this->encode($value);
        if ( $expire > 0 ) 
        {
            return $this->handler->setex($key, $expire, $value);
        }
        else 
        {
            return $this->handler->set($key, $value);
        }
    }

    public function get( $key )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->decode($this->handler->get($key));
    }

    public function hget( $key, $hash )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->decode($this->handler->hGet($key, $hash));
    }

    public function hset( $key, $hash, $value )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->handler->hSet($key, $hash, $this->encode($value));
    }

    public function hgetall( $key )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->decode($this->handler->hGetAll($key));
    }

    public function lpush( $key, $value )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->handler->lpush($key, $this->encode($value));
    }

    public function rpop( $key )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
 
        return $this->decode($this->handler->rpop($key));
    }

    public function rpush( $key, $value )
    {
        if (!$this->handler)
        {
            $this->connect();
        }
 
        return $this->handler->rpush($key, $this->encode($value));
    }

    public function lpop( $key )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->decode($this->handler->lpop($key));
    }

    public function lindex( $key, $index )
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        return $this->decode($this->handler->lindex($key, $index));
    }

    public function scan($keyword)
    {
        $keys = [];
        if( !empty($this->connect['is_cluster']) )
        {
            $keyword = self::$config['prefix'].$keyword;
            foreach ($this->handler->_masters() as $master) 
            {
                $iterator = null;
                while ($tmp = $this->handler->scan($iterator, $master, $keyword)) 
                {
                    $keys = array_merge($keys, $tmp);
                }
            }
        }
        else
        {
            $iterator = null;
            while ($tmp = $this->handler->scan($iterator, $keyword)) 
            {
                $keys = array_merge($keys, $tmp);
            }
        }
    
        return $keys;
    }

    public function infos()
    {
        $infos = [];
        if( !empty($this->connect['is_cluster']) )
        {
            foreach ($this->handler->_masters() as $master) 
            {
                $info = (array) $this->handler->info($master);
                foreach($info as $k => $v)
                {
                    $k = $k . '('.implode(":", $master).')';
                    $infos[$k] = $v;
                }
            }
        }
        else
        {
            $infos = $this->handler->info();
        }

        return $infos;
    }

    public function encode($value)
    {
        return !empty($this->connect['serializer']) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function decode($value)
    {
        if( empty($this->connect['serializer']) )
        {
            if ( is_array($value) ) 
            {
                foreach ($value as $k => $v) 
                {
                    $value[$k] = is_array($v) ? $v : json_decode($v, true);
                }
            }
            else
            {
                $value = is_array($value) ? $value : json_decode($value, true);
            }
        }

        return $value;
    }

    /**
     * cli模式加上进程ID,防止多进程实例串行
     * @param  string $name 实例名称
     * @return string       cli下带进程号的实例名称
     */
    public static function get_muti_name($name = 'redis')
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

    /**
     * 调用Redis其他方法
     *
     * @param $method
     * @param $arguments
     *
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
