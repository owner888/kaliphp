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

use kaliphp\cache;
use kaliphp\db;
use kaliphp\config;
use kaliphp\session;

class cls_auth 
{
    // 用户ID
    public $uid = 0;
    // 用户信息
    public $user = [];

    // 当前实例
    public static $_instances = [];
    // 配置信息
    public static $config = [];
    // 缓存前缀
    protected static $_cache_prefix = 'auth_user';
    // 验证句柄
    public static $auth_hand = 'auth_hand';
    // 用户表
    public static $table_config = [
        'user'          => '#PB#_admin',            // 用户表
        'user_group'    => '#PB#_admin_group',      // 用户组表
        'user_login'    => '#PB#_admin_login',      // 用户登录日志表
        'user_oplog'    => '#PB#_admin_oplog',      // 用户操作日志表
        'user_purview'  => '#PB#_admin_purview',    // 用户权限表
    ];
    // 用户表字段
    public static $table_fields = [
        'uid', 
        'groups',
        'username', 
        'password', 
        'realname',
        'avatar',
        'email',
        'roommaster',
        'session_id',
        'session_expire',
        'status' 
    ];

    public static function _init()
    {
        static::$config = config::instance('config')->get('purview');
        static::$config['cookie'] = config::instance('config')->get('cookie');
    }

    /**
	 * 创建实例
	 *
	 * @param   string    $name    Identifier for this mod_auth
	 * @param   array     $config  Configuration array
	 * @return  cls_validate
	 */
    public static function instance( $uid = 0 )
    {
        if ( isset(static::$_instances[$uid]) )
        {
            return static::$_instances[$uid];
        }

        static::$_instances[$uid] = new static($uid);
        return static::$_instances[$uid];
    }

    /**
     * 验证类必须扩展这个函数
     * 
     * @param string $ct    要验证的控制器
     * @param string $ac    要验证的控制器方法
     * @return void
     */
    public static function auth( string $ct, string $ac )
    {

    }

    /**
     * 检测用户登录
     *
     * @param string $account   登录账号：会员名、邮箱、手机
     * @param string $loginpwd  登录密码
     * @param int $remember     记住登录
     * @return array $userinfo  登录正常返回用户信息，否则抛异常
     */
    public function check_user( string $account, string $loginpwd, int $remember = 0 )
    {

    }

    /**
     * 获取用户具体信息
     *
     * @return mix array|false
     */
    public function get_user( string $account = null, string $ftype = 'uid', bool $use_cache = true )
    {
    }

    /**
     * 保存用户信息到数据库表
     * 
     * @param array $data
     * @return void
     */
    public function save_user( $data )
    {
        // 不要保存明文密码
        if ( isset($data['password']) && $data['password'] ) 
        {
            $data['password'] = static::password_hash($data['password']);
        }

        if ( $data['uid'] && $this->get_user($data['uid']) ) 
        {
            $uid = $data['uid'];
            unset($data['uid']);

            return db::update(static::$table_config['user'])
                ->set($data)
                ->where('uid', '=', $uid)
                ->execute();
        }
        else 
        {
            return db::insert(static::$table_config['user'])
                ->set($data)
                ->execute();
        }
    }

    /**
     * 检测权限
     * 
     * @param string $mod
     * @param string $action
     * @param int $backtype     返回类型， 1--是由权限控制程序直接处理
     * @return mixed            对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview( string $mod, string $action, int $backtype = 1 )
    {

    }

    /**
     * 检查密码
     * 
     * @param string $password          明文
     * @param string $hash_password     密文
     * @return bool
     */
    public static function check_password( string $password, string $hash_password )
    {
        return password_verify( $password , $hash_password );
    }

    /**
     * 会员密码加密方式接口（默认是 BCRYPT）
     */
    public static function password_hash( string $password )
    {
        return password_hash( $password, PASSWORD_BCRYPT );
    }

    /**
     * 记录一下用户ID到session和cookie，用户ID和用户数据已经在上一步保存了
     * @parem $rows  用户信息
     * @parem $keeptime 登录状态保存时间
     * @return bool
     */
    public function set_logininfo( $row )
    {
        if( !is_array( $row ) || !isset($row['uid']) )
        {
            return false;
        }

        if( static::$config['auttype'] == 'session' )
        {
            $_SESSION[static::$auth_hand.'_uid'] = $row['uid'];
            static::set_cookie('uid', $row['uid'], static::$config['cookie']['expire']);
        }

        $this->uid = $row['uid'];
        return true;
    }

    /**
     * 销毁登陆信息
     * 
     * @param mixed $uid
     * @return void
     */
    public function del_logininfo( $uid = null )
    {
        $uid = $uid == null ? $this->uid : $uid;

        $session_id = db::select('session_id')
            ->from(static::$table_config['user'])
            ->where('uid', $uid)
            ->as_field()
            ->execute();

        // 删除服务器上session数据
        session::del( $session_id );
        // 删除用户缓存数据
        $this->del_cache( $uid );
        // 删除TOKEN缓存信息
        static::del_token_uid( $session_id );

        // 删除 session_id 值
        db::update(static::$table_config['user'])
            ->set([
                'session_id' => '',
            ])
            ->where('uid', $uid)
            ->execute();
    }

    /**
     * 设置 token 对应的 uid
     * 
     * @param mixed $token
     * @param mixed $uid
     * @param float $expire
     * @return void
     */
    public static function set_token_uid( $token, $uid, $expire = 3600 )
    {
        cache::set("uid:{$token}", $uid, $expire);
    }

    /**
     * 获取 token 对应的 uid
     * 
     * @param mixed $token
     * @param mixed $uid
     * @param float $expire
     * @return void
     */
    public static function get_token_uid( $token )
    {
        $uid = cache::get("uid:{$token}");
        return $uid;
    }

    /**
     * 删除 token 对应的 uid
     * 
     * @param mixed $token
     * @param mixed $uid
     * @param float $expire
     * @return void
     */
    public static function del_token_uid( $token )
    {
        cache::del("uid:{$token}");
    }

    /**
     * 保存一个cookie值
     * $key, $value, $keeptime
     */
    public static function set_cookie( $key, $value, $keeptime = 0, $encode = true )
    {
        $keeptime = $keeptime==0 ? null : time()+$keeptime;
        $key = static::$auth_hand.'_'.$key;
        setcookie($key, $value, $keeptime, static::$config['cookie']['path'], static::$config['cookie']['domain']);
        // 数据调用是调用这个加密的数据，然后解密
        if ( $encode )
        {
            $key = $key.'_kaliphp';
            $value = substr(md5(static::$config['cookie']['pwd'].$value), 0, 24);
            setcookie($key, $value, $keeptime, static::$config['cookie']['path'], static::$config['cookie']['domain']);
        }
    }

    /**
     * 删除cookie值
     *
     * @parem $key
     */
    public static function del_cookie( $key, $encode = true )
    {
        $key = static::$auth_hand.'_'.$key;
        setcookie($key, '', time()-3600, static::$config['cookie']['path'], static::$config['cookie']['domain']);
        if( $encode )
        {
            setcookie($key.'_kaliphp', '', time()-3600, static::$config['cookie']['path'], static::$config['cookie']['domain']);
        }
    }

    /**
     * 获得经过加密对比的cookie值
     *
     * @parem $key
     */
    public static function get_cookie( $key, $encode = true )
    {
        $key = static::$auth_hand.'_'.$key;
        if( !isset($_COOKIE[$key]) ) { return ''; }

        // 加密的话先对比一下加密值是否一样，是才返回真正结果
        if( $encode )
        {
            if( !isset($_COOKIE[$key.'_kaliphp']) ) { return ''; }

            $epwd = substr( md5(static::$config['cookie']['pwd'].$_COOKIE[$key]), 0, 24 );
            return ($_COOKIE[$key.'_kaliphp'] != $epwd ) ? '' : $_COOKIE[$key];
        }
        else
        {
            return $_COOKIE[$key];
        }
    }

    /**
     * 设置用户缓存
     *
     * @return bool
     */
    public function get_cache( $uid = null )
    {
        $uid = $uid == null ? $this->uid : $uid;
        return cache::get(static::$_cache_prefix.'-'.$uid);
    }

    /**
     * 设置用户缓存
     *
     * @return bool
     */
    public function set_cache( $user, $uid = null )
    {
        $uid = $uid == null ? $this->uid : $uid;
        $user['lastip'] = IP;
        $user['lasttime'] = time();
        cache::set(static::$_cache_prefix.'-'.$uid, $user);
    }

    /**
     * 删除用户缓存
     *
     * @return bool
     */
    public function del_cache( $uid = null )
    {
        $uid = $uid == null ? $this->uid : $uid;
        // 删除用户缓存信息
        cache::del(static::$_cache_prefix.'-'.$uid);
        // 删除用户权限信息
        cache::del(static::$_cache_prefix.'_purview_mods'.'-'.$uid);
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        // 销毁登陆信息
        $this->del_logininfo();

        if ( static::$config['auttype'] == 'session' )
        {
            // 清空SESSION
            if( !empty($_SESSION[static::$auth_hand.'_uid']) ) 
            {
                $_SESSION[static::$auth_hand.'_uid'] = '';
                session_destroy();
            }
            // 删除COOKIE中的uid
            static::del_cookie('uid');
        }

        return true;
    }


}
