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
 
    // 遇锁立刻返回
    if (!cls_redis_lock::lock('test'))
    {
        show_error();
        return;
    }
    do_job();
    cls_redis_lock::unlock('test');

    // 遇锁等待3秒
    if (cls_redis_lock::lock('test', 3))
    {
        do_job();
        cls_redis_lock::unlock('test');
    }

 */
class cls_redis_lock
{
    /**
     * 加锁
     *
     * @param  string $name           锁的标识名
     * @param  int $timeout           循环获取锁的等待超时时间，在此时间内会一直尝试获取锁直到超时，为 0 表示失败后直接返回不等待
     * @param  int $expire            锁的生存时间(秒)，必须大于0，超过生存时间锁仍未被释放，系统会自动强制释放
     * @param  int $wait_interval_us  获取锁失败后挂起再试的时间间隔(微秒)，1秒=1000毫秒=1000,000微妙
     *
     * @return bool
     */
    public static function lock(string $name, int $timeout = 0, int $expire = 15, int $wait_interval_us = 100000): bool
    {
        if ($name == null) return false;

        // 取得当前时间
        $now = time();
        // 获取锁失败时的等待超时时刻
        $timeout_at = $now + $timeout;
        // 锁的最大生存时刻
        $expire_at = $now + $expire;

        $redis_key = "Lock:{$name}";
        while (true)
        {
            // 将rediskey的最大生存时刻存到redis里，过了这个时刻该锁会被自动释放
            $ret = cls_redis::instance()->setnx($redis_key, $expire_at);
            if ($ret != false)
            {
                // 设置key的失效时间
                cls_redis::instance()->expire($redis_key, $expire);
                return true;
            }

            // 以秒为单位，返回给定key的剩余生存时间
            $ttl = cls_redis::instance()->ttl($redis_key);
            // ttl小于0 表示key上没有设置生存时间（key是不会不存在的，因为前面setnx会自动创建）
            // 如果出现这种状况，那就是进程的某个实例setnx成功后 crash 导致紧跟着的expire没有被调用
            // 这时可以直接设置expire并把锁纳为己用
            if ($ttl < 0)
            {
                cls_redis::instance()->set($redis_key, $expire_at);
                return true;
            }

            // 循环请求锁部分
            // 如果没设置锁失败的等待时间 或者 已超过最大等待时间了，那就退出
            if ($timeout <= 0 || $timeout_at < microtime(true)) break;

            // 隔 $wait_interval_us 后继续请求
            usleep($wait_interval_us);
        }

        return false;
    }

    /**
     * 解锁
     *
     * @param  string $name 锁的标识名
     * @param  bool $muti_threads 是否多进程，如果要解锁不同进程，设置为true
     *
     * @return bool
     */
    public static function unlock(string $name): bool
    {
        // 先判断是否存在此锁
        if ( self::is_locking($name) )
        {
            // 删除锁
            if (cls_redis::instance()->del("Lock:$name"))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 给当前所增加指定生存时间，必须大于0
     *
     * @param  string $name 锁的标识名
     *
     * @return bool
     */
    public static function expire(string $name, int $expire): bool
    {
        // 先判断是否存在该锁
        if (self::is_locking($name))
        {
            // 所指定的生存时间必须大于0
            $expire = max($expire, 1);
            // 增加锁生存时间
            if (cls_redis::instance()->expire("Lock:$name", $expire))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断当前是否拥有指定名字的锁
     *
     * @param  string  $name 锁的标识名
     *
     * @return bool
     */
    public static function is_locking(string $name): bool
    {
        // 从redis返回该锁的生存时间
        return cls_redis::instance()->get("Lock:$name");
    }

}
