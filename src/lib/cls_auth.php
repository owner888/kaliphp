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

namespace kaliphp\lib;

use kaliphp\cache;
use kaliphp\db;
use kaliphp\req;
use kaliphp\util;
use kaliphp\config;
use kaliphp\lib\cls_filter;

use Exception;

class cls_auth 
{
    // 缓存前缀
    protected static $_cache_prefix = 'auth_user';
    // 验证句柄
    public static $auth_hand = 'auth_hand';
    // 登录错误次数
    public static $login_error_num = 3;
    // 用户表
    public static $table_config = [
        'user'          => '#PB#_admin',            // 用户表
        'user_group'    => '#PB#_admin_group',      // 用户组表
        'user_login'    => '#PB#_admin_login',      // 用户登录日志表
        'user_oplog'    => '#PB#_admin_oplog',      // 用户操作日志表
        'user_purview'  => '#PB#_admin_purview',    // 用户权限表
        'user_session'  => '#PB#_user_session',     // 用户session表
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
        'session_id',
        'session_expire',
        'status'
    ];

    // 当前实例
    public static $_instances = [];
    // 配置信息
    public static $config = [];

    // 用户ID
    public $uid = 0;
    // 用户信息
    public $user = [];

    public static $os           = null;    // 当前系统
    public static $version      = null;    // API版本
    public static $utma         = null;    // 设备ID
    public static $device_type  = null;    // 设备类型 1、游戏 2、助手 3、小程序 4、微信回调
    public static $os_version   = null;    // 系统版本
    public static $device       = null;    // 设备型号

    public static function _init()
    {
        static::$config = config::instance('config')->get('purview');
        static::$config['cookie'] = config::instance('config')->get('cookie');

        self::$os          = req::item('os',          null, 'strtolower');  // 当前系统
        self::$version     = req::item('version',     null, 'string');      // 当前版本
        self::$utma        = req::item('utma',        null, 'string');      // 设备ID    
        self::$device_type = req::item('device_type', null, 'string');      // 设备类型
        self::$device      = req::item('device',      null, 'string');      // 设备型号  
        self::$os_version  = req::item('os_version',  null, 'string');      // 当前版本
    }

    /**
     * 创建实例
     *
     * @param   string $name    实例名
     * @param   array  $config  实例配置
     *
     * @return  cls_auth
     */
    public static function instance($uid = 0)
    {
        if ( isset(static::$_instances[$uid]) )
        {
            return static::$_instances[$uid];
        }

        static::$_instances[$uid] = new static($uid);
        return static::$_instances[$uid];
    }

    /**
     * 检测用户登录
     * 登录接口才会到这里来
     *
     * @param string $account    登录账号：会员名、邮箱、手机
     * @param string $loginpwd   登录密码
     * @param int    $remember   记住登录
     *
     * @return array $userinfo   登录正常返回用户信息，否则抛异常
     */
    public function check_user(string $account, string $loginpwd, int $remember = 0)
    { 
        if ( $account == '' || $loginpwd == '' )
        {
            throw new Exception('请输入会员名密码');
        }

        $user = [];

        // 存在登录表，一般用于前端接口，登录表和用户信息表分开
        if ( isset(static::$table_config['user_account'])) 
        {
            $uid = db::select('uid')
                ->from(static::$table_config['user_account'])
                ->where('account', $account)
                ->as_field()
                ->execute(true);

            if ( !$uid || false == ($user = $this->get_user($uid, 'uid', false)) )
            {
                throw new Exception("输入的账号信息有误", -1);
            }
        }
        else 
        {
            // 检测用户名合法性.
            $ftype = 'username';
            if ( cls_validate::instance()->email($account) )
            {
                $ftype = 'email';
            }
            else if ( !cls_validate::instance()->username($account) )
            {
                throw new Exception('会员名格式不合法！');
            }

            if ( false == ($user = $this->get_user($account, $ftype, false)) )
            {
                throw new Exception("输入的账号信息有误", -1);
            }
        }

        // 同一IP使用某帐号连续错误次数检测
        if ( $this->get_login_error24($account) )
        {
            throw new Exception('连续登录失败超过3次，暂时禁止登录！');
        }
        //用户被禁用
        else if ( !$user['status'] ) 
        {
            throw new Exception ('用户禁用！');
        }
        // 正常密码，正确生成会话信息
        else if ( 
            false == static::check_password($loginpwd, $user['password']) ||
            false == ($user = $this->auth_user($user, $remember))
        )
        {
            // 失败直接把form表单过来的赋值，因为 $user 为空
            $user['username'] = $account;
            $this->save_login_history($user, 0);
            throw new Exception('用户名或密码无效');
        }

        return $user;
    }

    /**
     * 添加/更新用户登陆session
     * 大于0表示成功
     * @param  array  $data   SESSION DATA
     * @param  array  $where  只有强制为只更新模式才需要 
     *
     * @return int       
     */
    public static function save_user_session(array $data, array $where = [])
    {
        $data_filter = cls_filter::data([
            'uid'           => ['type' => 'text', 'default' => null],
            'username'      => ['type' => 'text', 'default' => null, 'callback' => 'trim'],
            'version'       => ['type' => 'text', 'default' => null],
            'device'        => ['type' => 'text', 'default' => null],
            'device_type'   => ['type' => 'int',  'default' => null],
            'os_version'    => ['type' => 'text', 'default' => null],
            'os'            => ['type' => 'text', 'default' => null],
            'address'       => ['type' => 'text', 'default' => null],
            'loginip'       => ['type' => 'text', 'default' => null],
            'logintime'     => ['type' => 'int',  'default' => time()],
            'online'        => ['type' => 'text', 'default' => null],
            'conn_ip'       => ['type' => 'text', 'default' => null],
            'conn_time'     => ['type' => 'int',  'default' => null],
            'app_name'      => ['type' => 'text',  'default' => null],
            'token' => [
                'type'      => 'text', 'default' => null, 
                'required'  => empty($data['client_id']) && empty($data['utma'])
            ],
            'utma' => [
                'type'      => 'text', 'default' => null, 
                'required'  => empty($data['client_id']) && empty($data['token'])
            ],
            'client_id' => [
                'type'      => 'text', 'default' => null, 
                'required'  => empty($data['token']) && empty($data['utma'])
            ],

            '_config_' => ['filter_null' => true],
        ], $data);
 
        // 过滤完数据为空
        if ( !is_array($data_filter) ) return false;
        try 
        {
            // 只更新模式
            if ( $where ) 
            {
                $status = db::update(static::$table_config['user_session'])
                    ->set($data_filter)
                    ->where($where)
                    ->execute();
            }
            //没有就插入，有就更新
            else
            {
                $dups = array_diff_key($data_filter, [
                    'logintime' => null,
                ]);

                [, $status] = db::insert(static::$table_config['user_session'])
                    ->set($data_filter)
                    ->dup($dups)
                    ->ignore(true)
                    ->execute();
            }
        } 
        catch (\Exception $e) 
        {
            $status = min(-1, $e->getCode());
        }

        return $status;
    }

    /**
     * 获取用户session列表
     * @param  array  $data 
     * @return array
     */
    public static function list_user_session(array $data):array
    {
        $data_filter = cls_filter::data([
            'uid' => [
                'type' => 'text', 'default' => null,
                'required' => empty($data['client_id']) && empty($data['utma']) && empty($data['token'])
            ],
            'device_type' => ['type' => 'int', 'default' => null],
            'token' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['client_id']) && empty($data['utma']) && empty($data['uid'])
            ],
            'client_id' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['token']) && empty($data['utma']) && empty($data['uid'])
            ],
            'utma' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['token']) && empty($data['client_id']) && empty($data['uid'])
            ],
            'id' => [
                'type' => 'text', 'default' => null],
            '_config_' => ['filter_null' => true],
        ], $data);
    
        if ( !is_array($data_filter) ) return [];

        $where = [];
        foreach ($data_filter as $filed => $val)
        {
            $where[] = [$filed, is_array($val) ? 'IN' : '=', $val];
        }

        $ret = (array) db::select($data['fields'] ?? '*')
            ->from(static::$table_config['user_session'])
            ->where($where)
            ->execute();
    
        return $ret;
    }

    /**
     * 获取用户token记录
     * @param  string $token 
     * @param  string $type  
     * @return bool
     */
    public static function get_user_session(string $token, string $type = 'token')
    {
        // 只能按照下面这几种类型删除
        $vali_types = ['token', 'utma', 'client_id', 'uid', 'id'];
        if ( !in_array($type, $vali_types) ) return false;
        $data = static::list_user_session([$type => $token]);
        //非按用户取，返回一条数据
        if ( $type != 'uid ') 
        {
            $data = reset($data);
        }

        return $data;
    }

    /**
     * 删除session
     * @param  array  $data 
     * @return 大于0表示成功
     */
    public static function delete_user_session(array $data)
    {
        $data_filter = cls_filter::data([
            'uid' => [
                'type' => 'text', 'default' => null,
                'required' => empty($data['client_id']) && empty($data['utma']) && empty($data['token'])
            ],
            'device_type' => ['type' => 'int', 'default' => null],
            'token' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['client_id']) && empty($data['utma']) && empty($data['uid'])
            ],
            'client_id' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['token']) && empty($data['utma']) && empty($data['uid'])
            ],
            'utma' => [
                'type' => 'text', 'default' => null, 
                'required' => empty($data['token']) && empty($data['client_id']) && empty($data['uid'])
            ],
            'app_name' => ['type' => 'text', 'default' => self::$config['app_name'] ?? null],
            'id' => ['type' => 'text', 'default' => null],
            '_config_' => ['filter_null' => true],
        ], $data);

        $where = [];
        foreach ($data_filter as $filed => $val)
        {
            $where[] = [$filed, is_array($val) ? 'IN' : '=', $val];
        }

        if ( !$where ) return false;
        $status = db::delete(static::$table_config['user_session'])
            ->where($where)
            ->execute();

        return $status;
    }

    /**
     * 删除用户session
     * @param  string $token 
     * @param  string $type  
     * @return bool
     */
    public static function delete_user_session_by_type(?string $token, string $type = 'token')
    {
        //只能按照下面这几种类型删除
        $vali_types = ['token', 'utma', 'client_id'];
        if ( !in_array($type, $vali_types)) return false;
        return self::delete_user_session([$type => $token]);
    }

    /**
     * 验证登录成功后对用户进行授权
     * 登录接口才会到这里来
     *
     * @param array $user   用户信息 
     * @param int $remember 是否自动登陆 
     * @param int $seclogin 是否私密登陆 
     * 
     * @return mixed array|false
     */
    public function auth_user(array $user, $remember = 0, $seclogin = 0): array
    {
        $user['remember'] = $remember;
        $user['seclogin'] = $seclogin;

        // 干掉敏感字段
        unset($user['password']);

        // 重新生成一下SESSION ID，这样可以保证每次登陆的SESSION ID都不同
        session_regenerate_id();

        $this->store_client_uid($user['uid']);
        $this->set_cache($user, $user['uid']);
        $this->save_login_history($user, 1);

        // 返回即可，不需要更新到用户缓存，否则每个端登录都会替换缓存的数据，没有意义
        $user['session_id'] = session_id();
        // 需要更新到数据库一份
        $this->save_user(['uid' => $user['uid'], 'session_id' => $user['session_id']]);
        // 如果配置了需要绑定uid,才会进行uid和token的绑定，方便通过uid操作token
        if ( !empty(self::$config['app_name']) && !empty(static::$table_config['user_session']) ) 
        {
            // 插入用户登陆session
            static::$utma && static::save_user_session([
                'uid'         => $user['uid'],
                'utma'        => static::$utma,
                'token'       => session_id(),
                'username'    => $user['username'],
                'address'     => $_SERVER['COUNTRY_LONG'] ?? COUNTRY,
                'version'     => static::$version,
                'os_version'  => static::$os_version,
                'os'          => static::$os,
                'device'      => static::$device,
                'device_type' => static::$device_type, 
                'loginip'     => req::ip(),
                'logintime'   => time(),
                'app_name'    => self::$config['app_name'],
            ]);
        }
  
        return $user;
    }

    /**
     * 验证用户是否有控制器和方法访问权限
     * 非登录接口统一验证权限用
     * 
     * @param string $ct       要验证的控制器
     * @param string $ac       要验证的控制器方法
     *
     * @return cls_auth $auth  当前类
     */
    public static function auth(string $ct, string $ac)
    {
        $uid = self::get_uid();

        /** @var cls_auth $auth */
        $auth = static::instance($uid);
        $auth->check_purview($ct, $ac);

        if ( !in_array($ac, ['logout', 'login', 'authentication']) ) 
        {
            $auth->uid  = $uid;
            $auth->user = $auth->get_user();

            // 登陆IP不在白名单，禁止操作
            if (  
                PHP_SAPI != 'cli' && 
                !empty($auth->user['safe_ips']) && 
                !in_array(IP, explode(',', str_replace('，', ',', $auth->user['safe_ips']))) 
            ) 
            {
                $msg = "IP不在白名单内，无法操作";
                if ( req::is_json() ) 
                {
                    util::response_error(-1, $msg);
                }
                else 
                {
                    cls_msgbox::show('用户权限限制', $msg, '');
                }
            }
        }

        return $auth;
    }

    /**
     * 保存用户ID到COOKIE和SESSION
     *
     * @param $uid  用户ID
     * @param $keeptime 登录状态保存时间
     *
     * @return bool
     */
    public function store_client_uid( $uid, $keeptime = null )
    {
        if ( empty($uid) )
        {
            return false;
        }

        $keeptime = $keeptime ?? static::$config['cookie']['expire'];
        // 不管是web session 还是 api token，都保存到session
        $_SESSION[self::_session_uid_key()] = $uid;
        static::set_cookie('uid', $uid, $keeptime);

        return true;
    }

    /**
     * 获取用户具体信息
     *
     * @return mixed array|false
     */
    public function get_user( ?string $account = null, string $ftype = 'uid', bool $use_cache = true )
    {
        if ( $account === null )
        {
            $account = $this->uid;
            $ftype   = 'uid';
        }

        if ( $ftype != 'uid' )
        {
            // 获取用户ID
            $uid = db::select('uid')
                ->from(static::$table_config['user'])
                ->where($ftype, '=', $account)
                ->as_field()
                ->execute();
        }
        else
        {
            $uid = $account;
        }

        $user = [];
        if ( $use_cache )
        {
            // 缓存读取
            $user = $this->get_cache($uid);
        }

        // 源数据
        if ( $uid && !$user )
        {
            // 读取用户数据
            $user = db::select(static::$table_fields)
                ->from(static::$table_config['user'])
                ->where('uid', '=', $uid)
                ->as_row()
                ->execute();
            // 用户存在
            if ( $user )
            {
                $this->set_cache($user, $uid);
            }
        }

        // 统一在 get_user 处理，其他地方不需要处理
        if ( isset($user['avatar']) && $user['avatar']) 
        {
            $user['avatar'] = self::get_user_avatar($user['avatar']);
        }
        return $user;
    }

    // 获取用户头像
    public static function get_user_avatar($avatar_url)
    {
        if (cls_validate::instance()->url($avatar_url))
        {
            return $avatar_url;
        }

        return util::get_img_url($avatar_url, 'jpg');
    }

    /**
     * 获取随机头像

     * @param  string  $uid        唯一ID
     * @param  integer $width      宽度
     * @param  integer $height     高度
     * @param  string  $avatar_dir 保存目录
     *
     * @return string  返回相对upload的头像         
     */
    public static function get_random_avatar($uid, $width = 200, $height = 200, $avatar_dir = 'avatar')
    {
        $filepath = config::instance('upload')->get('filepath');
        $avatar_path = $filepath . '/' . $avatar_dir;
        util::path_exists($avatar_path);

        //获取随机头像
        $avatar  = $avatar_dir .'/' . md5($uid) . '.jpg';
        $imgdata = file_get_contents("https://picsum.photos/{$width}/{$height}?random=1&?blur");
        file_put_contents($filepath. '/' . $avatar, $imgdata);

        return $avatar;
    }

    /**
     * 保存用户信息到数据库表
     * 
     * @param array $data
     * @return void
     */
    public function save_user($data)
    {
        // 明文加密字段
        foreach (['password', 'fake_password', 'onetime_password'] as $f)
        {
           if ( isset($data[$f]) ) $data[$f] = static::password_hash($data[$f]);
        }

        $dups = $data;
        // 不可以修改的字段
        foreach (['uid', 'regtime', 'regip'] as $f)
        {
           if ( isset($dups[$f]) ) unset($dups[$f]);
        }

        $data['uid']    = isset($data['uid']) ? $data['uid'] : util::random('web', 16);
        list(, $status) = db::insert(static::$table_config['user'])
           ->set($data)
           ->dup($dups)
           ->execute();

        return $status > 0 ? true : false;
    }

    /**
     * 检测权限
     * 
     * @param $ct      控制器
     * @param $ac      控制器方法
     * @param backtype 返回类型，1--是由权限控制程序直接处理 2--返回检查结果
     *
     * @return mixed            对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview( string $ct, string $ac, int $backtype = 1 )
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
     * 保存一个cookie值
     * $key, $value, $keeptime
     */
    public static function set_cookie($key, $value, $keeptime = 0, $encode = true)
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
     * @param $key
     */
    public static function del_cookie($key, $encode = true)
    {
        $key = static::$auth_hand.'_'.$key;
        setcookie($key, '', time()-3600, static::$config['cookie']['path'], static::$config['cookie']['domain'] ?? '');
        if ( $encode )
        {
            setcookie($key.'_kaliphp', '', time()-3600, static::$config['cookie']['path'], static::$config['cookie']['domain'] ?? '');
        }
    }

    /**
     * 获得经过加密对比的cookie值
     *
     * @param $key
     */
    public static function get_cookie($key, $encode = true)
    {
        $key = static::$auth_hand.'_'.$key;
        if ( !isset($_COOKIE[$key]) ) { return ''; }

        // 加密的话先对比一下加密值是否一样，是才返回真正结果
        if ( $encode )
        {
            if ( !isset($_COOKIE[$key.'_kaliphp']) ) { return ''; }

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
    public function get_cache($uid = null)
    {
        $uid = $uid ?? $this->uid;
        return cache::get(static::$_cache_prefix.'-'.$uid);
    }

    /**
     * 设置用户缓存
     *
     * @return bool
     */
    public function set_cache($user, $uid = null)
    {
        $uid = $uid ?? $this->uid;
        $user['lastip'] = IP;
        $user['lasttime'] = time();
        cache::set(static::$_cache_prefix.'-'.$uid, $user);
    }

    public function save_user_app_log($uid = null)
    {

    }
    
    /**
     * 删除用户缓存
     *
     * @return bool
     */
    public function del_cache($uid = null)
    {
        $uid = $uid ?? $this->uid;
        // 删除用户缓存信息
        cache::del(static::$_cache_prefix.'-'.$uid);
        // 删除用户权限信息
        cache::del(static::$_cache_prefix.'_purview_mods'.'-'.$uid);
    }

    private static function _session_uid_key()
    {
        return static::$auth_hand.'_uid';
    }

    /**
     * 获取当前uid
     * @return string | null
     */
    public static function get_uid()
    {
        return $_SESSION[self::_session_uid_key()] ?? 0;
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        // 清空SESSION
        if ( false != ($sess_id = self::get_uid()) ) 
        {
            // 如果配置了需要绑定uid,才会进行uid和token的绑定，方便通过uid操作token
            if ( !empty(self::$config['app_name']) && !empty(static::$table_config['user_session']) ) 
            {
                self::delete_user_session_by_type($sess_id);
            }
    
            $_SESSION[self::_session_uid_key()] = '';
            session_destroy();
        }
        // 删除COOKIE中的uid
        static::del_cookie('uid');
        // 删除用户缓存数据
        $this->del_cache($this->uid);

        return true;
    }

    /**
     * 保存历史登录记录 
     * 
     * @param array $user   用户信息 
     * @param int $loginsta 登录状态：0--失败 1--成功 
     * 
     * @return void
     */
    public function save_login_history(array $user, int $loginsta = 0, $session_id = null)
    {
        $ltime       = time();
        $loginip     = req::ip();
        $cli_hash    = md5($user['username'].'-'.$loginip);
        $user['uid'] = $user['uid'] ?? 0;

        if ( $loginsta == 1 ) 
        {
            db::update(static::$table_config['user'])
                ->set([
                    'session_id' => $session_id, // 保存 session_id 到数据库，用于后台随时踢出
                    'logintime'  => $ltime,
                    'loginip'    => $loginip,
                ])
                ->where('uid', $user['uid'])
                ->execute();
        }

        // 成功失败都需要记录，用于判断是否连续3次登录错误
        db::insert(static::$table_config['user_login'])
            ->set([
                'uid'          => $user['uid'],
                'username'     => $user['username'],
                'agent'        => req::user_agent(),
                'logintime'    => $ltime,
                'loginip'      => $loginip,
                'loginsta'     => $loginsta,
                'cli_hash'     => $cli_hash,
            ])
            ->execute();

        return true;
    }

    /**
     * 检测用户24小时内连续输错密码次数是否已经超过
     * 
     * @param string $username  用户名 
     * @param string $logintype 登录类型 
     * 
     * @return bool 超过返回true, 正常状态返回false
     */
    public function get_login_error24(string $username, string $logintype = 'cli_hash')
    {
        $starttime = strtotime( date('Y-m-d 00:00:00', time()) );
        if ( $logintype == 'cli_hash' ) 
        {
            $loginip  = req::ip();
            $loginval = md5($username.'-'.$loginip);
        }
        else 
        {
            $loginval = $username;
        }
        $rows = db::select('loginsta')
            ->from(static::$table_config['user_login'])
            ->where($logintype,  '=', $loginval)
            ->where('logintime', '>', $starttime)
            ->order_by('logintime', 'DESC')
            ->limit(static::$login_error_num)
            ->execute();

        if ( $rows === null || count($rows) < static::$login_error_num )
        {
            return false;
        }
        foreach ($rows as $row) 
        {
            // 最近3条有一条登录成功就不属于禁用账号
            if ( $row['loginsta'] > 0 ) 
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 删除用户24小时内连续输错密码日志 
     * 
     * @return void
     */
    public function del_login_error24()
    {
        $starttime = strtotime( date('Y-m-d 00:00:00', time()) );
        $rows = db::select('id, loginsta')
            ->from(static::$table_config['user_login'])
            ->where('uid',       '=', $this->uid)
            ->where('logintime', '>', $starttime)
            ->order_by('logintime', 'DESC')
            ->limit(static::$login_error_num)
            ->execute();

        if ( $rows === null || count($rows) < static::$login_error_num )
        {
            return false;
        }

        foreach ($rows as $row) 
        {
            // 最近3条有一条登录成功就不属于禁用账号
            if ( $row['loginsta'] > 0 ) 
            {
                return false;
            }
        }

        foreach ($rows as $row) 
        {
            $id = $row['id'];
            db::delete(static::$table_config['user_login'])
                ->where('id', '=', $id)
                ->execute();
        }

        return true;
    }

    /**
     * 获得用户上次登录时间和ip
     *
     * @return array
     */
    public function get_last_login()
    {
        $datas = db::select("loginip, logincountry, logintime")
            ->from(static::$table_config['user_login'])
            ->where('uid', $this->uid)
            ->and_where('loginsta', 1)
            ->order_by('logintime', 'desc')
            ->limit(2)
            ->offset(0)
            ->execute();
        if ( isset($datas[1]) )
        {
            return $datas[1];
        } 
        else 
        {
            return [
                'loginip'      => '', 
                'logincountry' => '-', 
                'logintime'    => 0
            ];
        }
    }

    /**
     *  保存管理日志
     *
     *  @param $msg 具体消息（如有引号，无需自行转义）
     *
     *  @return bool
     */
    public function save_admin_log($msg)
    {
        $url = '?ct='.req::item('ct').'&ac='.req::item('ac');
        foreach (req::$forms as $k => $v)
        {
            if ( preg_match('/pwd|password|sign|cert/', $k) || $k=='ct' || $k=='ac' ) 
            {
                continue;
            }
            $nstr = "&{$k}=".(is_array($v) ? 'array()' : $v);
            if ( strlen($url.$nstr) < 100 ) 
            {
                $url .= $nstr;
            } 
            else 
            {
                break;
            }
        }
        
        $user = $this->get_user();
        db::insert(static::$table_config['user_oplog'])
            ->set([
                'session_id' => session_id(),
                'uid'        => $user['uid'],
                'username'   => $user['username'],
                'msg'        => substr($msg, 0, 150),
                'do_ip'      => req::ip(),
                'do_country' => req::country(),
                'do_time'    => time(),
                'do_url'     => $url,
            ])
            ->execute();
    }
}
