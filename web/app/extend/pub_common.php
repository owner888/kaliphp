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

namespace extend;
use kaliphp\lib\log;

/**
 * 用户私有类
 */
class pub_common
{
    public static function test($string)
    {
        log::info($string);
    }

//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function send_log($message, $level)
//    {
//        log::log($message, $level);
//    }
//
//    /**
//     * 日志示例
//     * @param $message
//     * @param $level
//     */
//    public static function send_error($message, $level)
//    {
//        log::error($message, $level);
//    }
}
