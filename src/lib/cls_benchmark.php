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
 * 基准测试类
 *
 * @version $Id$  
 */
class cls_benchmark
{
    public static $marker = array();

    /**
     * 设置一个标识
     * 
     * @param mixed $name
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2017-01-12 11:30
     */
    public static function mark($name, $value = '')
    {
        // 记录时间和内存使用
        self::$marker[$name]['time'] = is_float($value) ? $value : microtime(true);
        self::$marker[$name]['mem']  = is_float($value) ? $value : memory_get_usage();
        self::$marker[$name]['peak'] = memory_get_peak_usage();
    }

    /**
     * 运行时间
     * 
     * @param string $point1
     * @param string $point2
     * @param int $decimals
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2017-01-12 11:30
     */
    public static function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
    {
        if ($point1 === '')
        {
            return '{elapsed_time}';
        }

        if ( ! isset(self::$marker[$point1]['time']))
        {
            return '';
        }

        if ( ! isset(self::$marker[$point2]['time']))
        {
            self::$marker[$point2]['time'] = microtime(TRUE);
        }

        return number_format(self::$marker[$point2]['time'] - self::$marker[$point1]['time'], $decimals);
    }

    /**
     * 运行内存
     * 
     * @param string $point1
     * @param string $point2
     * @param int $decimals
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2017-01-12 11:30
     */
    public static function elapsed_memory($point1 = '', $point2 = '', $decimals = 2)
    {
        if ($point1 === '')
        {
            return '{elapsed_memory}';
        }

        if ( ! isset(self::$marker[$point1]['mem']))
        {
            return '';
        }

        if ( ! isset(self::$marker[$point2]['mem']))
        {
            self::$marker[$point2]['mem'] = memory_get_usage();
        }

        return self::convert(self::$marker[$point2]['mem'] - self::$marker[$point1]['mem'], $decimals);
    }

    /**
     * 真实运行内存
     * 
     * @param string $point1
     * @param string $point2
     * @param int $decimals
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2017-01-12 11:30
     */
    public static function elapsed_memory_peak($point1 = '', $point2 = '', $decimals = 2)
    {
        if ($point1 === '')
        {
            return '{elapsed_memory_peak}';
        }

        if ( ! isset(self::$marker[$point1]['peak']))
        {
            return '';
        }

        if ( ! isset(self::$marker[$point2]['peak']))
        {
            self::$marker[$point2]['peak'] = memory_get_peak_usage();
        }

        return self::convert(self::$marker[$point2]['peak'] - self::$marker[$point1]['peak'], $decimals);
    }

    /**
     * Memory Usage
     * 
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2017-01-12 11:30
     */
    public static function memory_usage()
    {
        //return self::convert(memory_get_usage());
        return '{memory_usage}';
    }

    // 转换大小单位
    public static function convert($size, $decimals = 2)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), $decimals) . ' ' . $unit[$i];
    }

}

