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
	//验证类必须扩展这个函数
	public static function auth($ct, $ac);

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
     * @param mixed $account    登录账号：会员名、邮箱、手机
     * @param mixed $loginpwd   登录密码
     * @param float $remember   记住登录
     * @return array $userinfo  登录正常返回用户信息，否则抛异常
     */
    public function check_user( $account, $loginpwd, $remember = 0 );

    /**
     * 检测权限
     *
     * @parem $mod
     * @parem $action
     * @parem backtype 返回类型， 1--是由权限控制程序直接处理
     * @return int  对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview($mod, $action, $backtype = 1);

    /**
     * 注销登录
     */
    public function logout();

}