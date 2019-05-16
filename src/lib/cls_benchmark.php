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
    public static function mark($name)
    {
        self::$marker[$name] = microtime(TRUE);
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

        if ( ! isset(self::$marker[$point1]))
        {
            return '';
        }

        if ( ! isset(self::$marker[$point2]))
        {
            self::$marker[$point2] = microtime(TRUE);
        }

        return number_format(self::$marker[$point2] - self::$marker[$point1], $decimals);
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
    public static function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

}

