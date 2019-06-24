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

interface cls_auth 
{
    /**
     * 验证类必须扩展这个函数
     * 
     * @param string $ct    要验证的控制器
     * @param string $ac    要验证的控制器方法
     * @return void
     */
    public static function auth( string $ct, string $ac );

    /**
     * 检查密码
     * 
     * @param string $password          明文
     * @param string $hash_password     密文
     * @return bool
     */
    public static function check_password( string $password, string $hash_password );

    /**
     * 会员密码加密方式接口（默认是 md5）
     */
    public static function password_hash( string $password );

    /**
     * 检测用户登录
     *
     * @param string $account   登录账号：会员名、邮箱、手机
     * @param string $loginpwd  登录密码
     * @param int $remember     记住登录
     * @return array $userinfo  登录正常返回用户信息，否则抛异常
     */
    public function check_user( string $account, string $loginpwd, int $remember = 0 );

    /**
     * 检测权限
     * 
     * @param string $mod
     * @param string $action
     * @param int $backtype     返回类型， 1--是由权限控制程序直接处理
     * @return mixed            对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview( string $mod, string $action, int $backtype = 1 );

    /**
     * 注销登录
     */
    public function logout();

}
