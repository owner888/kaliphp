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
use kaliphp\lib\cls_redis;

/**
 * 在redis上实现分布式锁
 * 参考：https://mp.weixin.qq.com/s/WS3jO4AKktbra7x_QDu4VA
 */
class cls_redis_lock
{
    private static $locked_names = [];

    /**
     * 加锁
     * @param  [type]  $name           锁的标识名
     * @param  integer $timeout        循环获取锁的等待超时时间，在此时间内会一直尝试获取锁直到超时，为0表示失败后直接返回不等待
     * @param  integer $expire         当前锁的最大生存时间(秒)，必须大于0，如果超过生存时间锁仍未被释放，则系统会自动强制释放
     * @param  integer $wait_interval_us 获取锁失败后挂起再试的时间间隔(微秒)
     * @return [type]                  [description]
     */
    public static function lock($name, $timeout = 0, $expire = 15, $wait_interval_us = 100000)
    {
        if ($name == null) return false;

        //取得当前时间
        $now = time();
        //获取锁失败时的等待超时时刻
        $timeout_at = $now + $timeout;
        //锁的最大生存时刻
        $expire_at = $now + $expire;

        $redis_key = "Lock:{$name}";
        while (true)
        {
            //将rediskey的最大生存时刻存到redis里，过了这个时刻该锁会被自动释放
            $result = cls_redis::instance()->setnx($redis_key, $expire_at);
            if ($result != false)
            {
                //设置key的失效时间
                cls_redis::instance()->expire($redis_key, $expire);
                //将锁标志放到locked_names数组里
                self::$locked_names[$name] = $expire_at;
                return true;
            }

            //以秒为单位，返回给定key的剩余生存时间
            $ttl = cls_redis::instance()->ttl($redis_key);
            //ttl小于0 表示key上没有设置生存时间（key是不会不存在的，因为前面setnx会自动创建）
            //如果出现这种状况，那就是进程的某个实例setnx成功后 crash 导致紧跟着的expire没有被调用
            //这时可以直接设置expire并把锁纳为己用
            if ($ttl < 0)
            {
                cls_redis::instance()->set($redis_key, $expire_at);
                self::$locked_names[$name] = $expire_at;
                return true;
            }

            /*****循环请求锁部分*****/
            //如果没设置锁失败的等待时间 或者 已超过最大等待时间了，那就退出
            if ($timeout <= 0 || $timeout_at < microtime(true)) break;

            //隔 $wait_interval_us 后继续 请求
            usleep($wait_interval_us);
        }

        return false;
    }

    /**
     * 解锁
     * @param  [type] $name [description]
     * @param  bool $muti_threads 是否多进程，如果要解锁不同进程，设置为true
     * @return [type]       [description]
     */
    public static function unlock($name, $muti_threads = false)
    {
        //先判断是否存在此锁
        if (self::is_locking($name) || !empty($muti_threads) )
        {
            //删除锁
            if (cls_redis::instance()->delete("Lock:$name"))
            {
                //清掉locked_names里的锁标志
                unset(self::$locked_names[$name]);
                return true;
            }
        }
        return false;
    }

    /**
     * 释放当前所有获得的锁
     * @return [type] [description]
     */
    public static function unlock_all()
    {
        //此标志是用来标志是否释放所有锁成功
        $all_success = true;
        foreach (self::$locked_names as $name => $expire_at)
        {
            if (false === self::unlock($name))
            {
                $all_success = false;
            }
        }
        return $all_success;
    }

    /**
     * 给当前所增加指定生存时间，必须大于0
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function expire($name, $expire)
    {
        //先判断是否存在该锁
        if (self::is_locking($name))
        {
            //所指定的生存时间必须大于0
            $expire = max($expire, 1);
            //增加锁生存时间
            if (cls_redis::instance()->expire("Lock:$name", $expire))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断当前是否拥有指定名字的所
     * @param  [type]  $name [description]
     * @return boolean       [description]
     */
    public static function is_locking($name)
    {
        //先看locked_names[$name]是否存在该锁标志名
        if (isset(self::$locked_names[$name]))
        {
            //从redis返回该锁的生存时间
            return (string)self::$locked_names[$name] = (string)cls_redis::instance()->get("Lock:$name");
        }

        return false;
    }

}
