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

/** 
 * Twitter ID生成类 
 * SnowFlake的结构如下(每部分用-分开): 
 * 0 - 0000000000 0000000000 0000000000 0000000000 0 - 00000 - 00000 - 000000000000  
 * 1位标识，由于long基本类型在Java中是带符号的，最高位是符号位，正数是0，负数是1，所以id一般是正数，最高位是0 
 * 41位时间截(毫秒级)，注意，41位时间截不是存储当前时间的时间截，而是存储时间截的差值（当前时间截 - 开始时间截) 
 * 得到的值），这里的的开始时间截，一般是我们的id生成器开始使用的时间，由我们程序来指定的（如下下面程序IdWorker类的startTime属性）。41位的时间截，可以使用69年，年T = (1L << 41) / (1000L * 60 * 60 * 24 * 365) = 69 
 * 10位的数据机器位，可以部署在1024个节点，包括5位datacenterId和5位workerId 
 * 12位序列，毫秒内的计数，12位的计数顺序号支持每个节点每毫秒(同一机器，同一时间截)产生4096个ID序号 
 * 加起来刚好64位，为一个Long型。 
 * SnowFlake的优点是，整体上按照时间自增排序，并且整个分布式系统内不会产生ID碰撞(由数据中心ID和机器ID作区分)，并且效率较高，经测试，SnowFlake每秒能够产生26万ID左右。 
 */
//$id = cls_snowflake::instance(0, 1)->nextid();

class cls_snowflake
{

    // 开始时间戳（2015-01-01）
    const twepoch = 1420041600000;

    // 机器id所占的位数
    const worker_id_bits = 5;
    // 数据标识id所占的位数
    const datacenter_id_bits = 5;
    // 支持的最大机器id，结果是31 (这个移位算法可以很快的计算出几位二进制数所能表示的最大十进制数)
    const max_worker_id = -1 ^ (-1 << self::worker_id_bits);
    // 支持的最大数据标识id，结果是31
    const max_datacenter_id = -1 ^ (-1 << self::datacenter_id_bits);
    // 序列在id中占的位数
    const sequence_bits = 12;

    // 机器ID向左移12位
    const worker_id_shift = self::sequence_bits;
    // 数据标识id向左移17位(12+5)
    const datacenter_id_shift = self::sequence_bits + self::worker_id_bits;
    // 时间截向左移22位(5+5+12)
    const timestamp_left_shift = self::sequence_bits + self::worker_id_bits + self::datacenter_id_bits;
    // 生成序列的掩码，这里为4095 (0b111111111111=0xfff=4095)
    const sequence_mask = -1 ^ (-1 << self::sequence_bits);

    // 工作机器ID(0~31)
    private static $worker_id;
    // 数据中心ID(0~31)
    private static $datacenter_id;
    // 毫秒内序列(0~4095)
    private static $sequence = 0;
    // 上次生成ID的时间截
    private static $last_timestamp = -1;

    /**
     * @var cls_snowflake
     */
    private static $self = NULL;

    /**
     * @static
     * @return cls_snowflake
     */
    public static function instance( $worker_id = 1, $datacenter_id = 1 )
    {
        if (self::$self == NULL) 
        {
            self::$self = new self( $worker_id, $datacenter_id );
        }
        return self::$self;
    }

    /** 
     * 构造函数 
     * @param worker_id     工作ID (0~31) 
     * @param datacenter_id 数据中心ID (0~31) 
     */ 
    final public function __construct( $worker_id = 1, $datacenter_id = 1 )
    {
        if ( $worker_id > self::max_worker_id || $worker_id < 0 )
        {
            throw new \Exception("worker Id can't be greater than ".self::max_worker_id." or less than 0");
        }

        if ( $datacenter_id > self::max_datacenter_id || $datacenter_id < 0 )
        {
            throw new \Exception("datacenter Id can't be greater than ".self::max_datacenter_id." or less than 0");
        }

        self::$worker_id = $worker_id;
        self::$datacenter_id = $datacenter_id;

        //$str = sprintf("worker starting. timestamp left shift %d, datacenter id bits %d, worker id bits %d, sequence bits %d, workerid %d, datacenter %d",
            //self::timestamp_left_shift, self::datacenter_id_bits, self::worker_id_bits, self::sequence_bits, self::$worker_id, self::$datacenter_id);
        //echo $str."\n";
    }

    /**
     * 获得下一个ID (该方法是线程安全的)
     * 
     * @return SnowflakeId
     */
    public function nextid()
    {
        $timestamp = $this->time_gen();

        //如果当前时间小于上一次ID生成的时间戳，说明系统时钟回退过这个时候应当抛出异常
        if ($timestamp < self::$last_timestamp)
        {
            throw new Excwption("Clock moved backwards.  Refusing to generate id for ".(self::$last_timestamp-$timestamp)." milliseconds");
        }

        //如果是同一时间生成的，则进行毫秒内序列 
        if(self::$last_timestamp == $timestamp) 
        {
            self::$sequence = (self::$sequence + 1) & self::sequence_mask;
            //毫秒内序列溢出
            if (self::$sequence == 0) 
            {
                //阻塞到下一个毫秒,获得新的时间戳
                $timestamp = $this->til_next_millis(self::$last_timestamp);
            }
        }
        //时间戳改变，毫秒内序列重置 
        else 
        {
            self::$sequence  = 0;
        }

        //上次生成ID的时间截
        self::$last_timestamp  = $timestamp;
        //移位并通过或运算拼到一起组成64位的ID
        $nextid = ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::twepoch)) << self::timestamp_left_shift) | 
            ( self::$datacenter_id << self::datacenter_id_shift ) | 
            ( self::$worker_id << self::worker_id_shift ) | 
            self::$sequence;
        return $nextid;
    }

    /**
     * 阻塞到下一个毫秒，直到获得新的时间戳 
     * @param last_timestamp 上次生成ID的时间截 
     * @return 当前时间戳 
     */
    private function til_next_millis($last_timestamp) 
    {
        $timestamp = $this->time_gen();
        while ($timestamp <= $last_timestamp) 
        {
            $timestamp = $this->time_gen();
        }

        return $timestamp;
    }

    /**
     * 返回以毫秒为单位的当前时间 
     * @return 当前时间(毫秒)
     */
    public function time_gen()
    {
        $time = explode(' ', microtime());
        $time2= substr($time[0], 2, 3);
        return  $time[1].$time2;
    }
}
