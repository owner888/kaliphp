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

namespace common\extend;

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
        defined('SYS_DEBUG') or define('SYS_DEBUG', $_ENV['SYS_DEBUG'] ?? true);
        // Chrome调试环境
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', $_ENV['SYS_CONSOLE'] ?? false);
        // 开发环境  测试:dev 外网测试环境:pre 正式环境:pub
        defined('SYS_ENV') or define('SYS_ENV', $_ENV['SYS_ENV'] ?? 'dev');

        define('ERR_NOT_LOGIN', -10001); //未登录
        define('ERR_NO_PURVIEW', -10002); //没有权限
        define('ERR_INCORRECT_PARAMATER', -10003); // 参数错误
    }
}
