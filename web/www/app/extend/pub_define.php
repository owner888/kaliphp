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

namespace extend;

/**
 * 全局变量操作静态类
 */
class pub_define
{
    /**
     * 设置全局变量
     */
    public static function init()
    {
        // 调试环境
        defined('SYS_DEBUG') or define('SYS_DEBUG', true);
        // Chrome调试环境
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', false);
        // 开发环境  测试:dev 外网测试环境:pre 正式环境:pub
        defined('SYS_ENV') or define('SYS_ENV', 'dev');
    }
}
