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
use kaliphp\kali;
use kaliphp\db;
use kaliphp\log;
use kaliphp\req;
use kaliphp\util;
use kaliphp\config;
use kaliphp\cache;
use kaliphp\session;
use kaliphp\lib\cls_msgbox;
use app\model\mod_session;

/**
 * 管理员权限控制类
 *
 * @version $Id$
 */
class cls_auth
{
    public static $config = [];

    public static $table_config = [
        'user'          => '#PB#_admin',            // 用户表
        'user_group'    => '#PB#_admin_group',      // 用户组表
        'user_login'    => '#PB#_admin_login',      // 用户登录日志表
        'user_oplog'    => '#PB#_admin_oplog',      // 用户操作日志表
        'user_purview'  => '#PB#_admin_purview',    // 用户权限表
    ];

    // 用户表数据库字段
    public static $fields = [
        'uid', 
        'username', 
        'password', 
        'fake_password',
        'onetime_password',
        'realname',
        'email',
        'groups', 
        'safe_ips',
        'status', 
        'otp_auth',
        'otp_authcode',
        'date_expired', 
        'session_id',
        'session_expire',
        'need_audit',
        'is_first_login',
    ];

    public static $cookie_config = [];

    private static $_instances = [];

    // 缓存前缀
    private $_cache_prefix = 'auth_user';

    // 分析处理后的权限配置数组
    public $purview_config = array();
    
    public $uid = 0;
    public static $auth_hand = 'auth';

    public $user = []; //方便在公共模型统一取用户信息

    public static function _init()
    {
        self::$config = config::instance('config')->get('purview');
        self::$cookie_config = config::instance('config')->get('cookie');
    }

    /**
	 * 创建实例
	 *
	 * @param   string    $name    Identifier for this cls_auth
	 * @param   array     $config  Configuration array
	 * @return  cls_validate
	 */
    static function instance($uid = 0)
    {
        if (isset(self::$_instances[$uid]))
        {
            return self::$_instances[$uid];
        }

        $instance = new static($uid);

        self::$_instances[$uid] = $instance;

        return $instance;
    }

    /**
     * 构造函数，根据池的初始化检测用户登录信息
     *
     * @parem $uid  用户ID
     */
    public function __construct( $uid = 0 )
    {
        $this->uid = $uid;
        //echo __method__."\n";

        // 如果用户ID存在，获取登录后可访问的 控制器-方法
        if ( $this->uid ) 
        {
            $purviews = $this->get_purviews();

            if ( empty($purviews)) 
            {
                self::$config['private'] = '';
            }
            elseif ( $purviews == '*' ) 
            {
                self::$config['private'] = '*';
            }
            else 
            {
                self::$config['private'] = [];
                $purviews = explode(",", $purviews);
                foreach ($purviews as $purview) 
                {
                    $mods = explode('-', $purview);
                    self::$config['private'][$mods[0]][] = $mods[1];
                }
            }
        }
    }

    /**
     * 检测用户登录
     *
     * @param mixed $account    登录账号：会员名、邮箱、手机
     * @param mixed $loginpwd   登录密码
     * @param float $remember   记住登录
     * @return array $userinfo  登录正常返回用户信息，否则抛异常
     */
    public function check_user( $account, $loginpwd, $remember = 0 )
    {
        if( $account == '' || $loginpwd == '' )
        {
            throw new \Exception('请输入会员名密码');
        }

        // 检测用户名合法性.
        $ftype = 'username';
        if( cls_validate::instance()->email($account) )
        {
            $ftype = 'email';
        }
        else if( !cls_validate::instance()->username($account) )
        {
            throw new \Exception('会员名格式不合法！');
        }

        // 同一IP使用某帐号连续错误次数检测
        if( $this->get_login_error24( $account ) )
        {
            throw new \Exception('连续登录失败超过3次，暂时禁止登录！');
        }

        // 读取用户ID
        $user = $this->get_user( $account, $ftype, false );

        // 存在用户数据
        if( is_array($user) )
        {
            if ( !$user['status'] ) 
            {
                throw new \Exception ('用户禁用！');
            }

            // 自动登陆
            $user['remember'] = $remember;
            // 秘密登陆
            $user['seclogin'] = 0;

            $loginsta = false;
            // 伪装密码，正确生成会话信息
            if( self::check_password($loginpwd, $user['fake_password']) )
            {
                // 私密登录
                $user['seclogin'] = 1;

                $loginsta = true;
            }
            // 一次性密码
            elseif( self::check_password($loginpwd, $user['onetime_password']) )
            {
                // 一次性密码首次登录也不需要更新密码
                $user['is_first_login'] = 0;

                // 删除一次性密码
                $this->save_user([
                    'uid' => $user['uid'],
                    'onetime_password' => null,
                ]);

                $loginsta = true;
            }
            // 正常密码，正确生成会话信息
            elseif( self::check_password($loginpwd, $user['password']) )
            {
                $loginsta = true;
            }

            // 登录成功
            if ( $loginsta ) 
            {
                $this->save_login_history($user, 1);
                $this->set_cache($user, $user['uid']);
                return $user;
            }
            //密码错误，保存登录记录
            else
            {
                $this->save_login_history($user, 0);
                throw new \Exception ('用户名或密码无效');
            }
        }
        //不存在用户数据时不进行任何操作
        else
        {
            $user['username'] = $account;
            $this->save_login_history($user, 0);
            throw new \Exception ('用户名或密码无效');
        }
    }

    /**
     * 检测权限
     *
     * @parem $mod
     * @parem $action
     * @parem backtype 返回类型， 1--是由权限控制程序直接处理
     * @return int  对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview($mod, $action, $backtype = 1)
    {
        // 未登录用户
        $rs = 0;

        // 获取检测应用池开放权限的模块
        $public_mod = isset(self::$config['public'][$mod]) ? self::$config['public'][$mod] : array();
        // 检测开放的控制器和方法
        if( !empty(self::$config['public']) && 
            ( self::$config['public']=='*' || in_array($action, $public_mod) || in_array('*', $public_mod) ) )
        {
            $rs = 1;
        }
        // 未登录用户
        else if( empty($this->uid) )
        {
            $rs = 0;
        }
        // 已登陆用户进行具体的权限检测
        else
        {
            // 检测开放控制器和事件（即是登录用户允许访问的所有公共事件）
            $protected_mod = isset(self::$config['protected'][$mod]) ? self::$config['protected'][$mod] : array();

            if ( !empty(self::$config['protected']) && 
                ( self::$config['protected']=='*' || in_array($action, $protected_mod) || in_array('*', $protected_mod) ) )
            {
                $rs = 1;
            }
            else
            {
                //echo '<pre>';print_r(self::$config['private']);echo '</pre>';
                // 检测开放控制器和事件（即是登录用户允许访问的所有公共事件）
                $private_mod = isset(self::$config['private'][$mod]) ? self::$config['private'][$mod] : array();
                if ( !empty(self::$config['private']) && 
                    ( self::$config['private']=='*' || in_array($action, $private_mod) || in_array('*', $private_mod) ) )
                {
                    $rs = 1;
                }
                else
                {
                    $rs = -1;
                }
            }
        }

        // 返回检查结果
        if( $backtype == 2 )
        {
            return $rs;
        }
        // 直接处理异常
        else
        {
            // 正常状态
            if( $rs == 1 )
            {
                return true;
            }
            // 用户权限不足(用户权限+用户组权限)
            else if( $rs == -1 )
            {
                if ( kali::$is_ajax ) 
                {
                    util::return_json(array(
                        'code' => -1,
                        'msg'  => '权限不足, 对不起，你没权限执行本操作！'
                    ));
                }
                else 
                {
                    cls_msgbox::show('用户权限限制', '权限不足, 对不起，你没权限执行本操作！', '');
                }
            }
            // 未登录用户
            else if( $rs == 0 )
            {
                if ( kali::$is_ajax ) 
                {
                    util::return_json(array(
                        'code' => -1,
                        'msg'  => '用户未登陆！'
                    ));
                }
                else 
                {
                    $jumpurl = self::$config['login_url'];
                    exit(header("Location:$jumpurl"));
                }

            }
        }
    }

    /**
     * 获取用户私有权限(非组权限)
     *
     * @return array (如果用户尚未登录，则返回 false )
     *
     */
    public function get_purviews()
    {
        // 缓存
        $cache_key = $this->_cache_prefix.'_purview_mods'.'-'.$this->uid;
        $purviews  = cache::get($cache_key);
        //$purviews  = false;

        // 源数据
        if( $purviews === false )
        {
            // 用户权限 = 用户权限 + 组权限
            // 用户权限
            $fields = db::select('purviews')
                ->from(static::$table_config['user_purview'])
                ->where('uid', '=', $this->uid)
                ->as_row()
                ->execute();
            $purviews = !empty($fields['purviews']) ? explode(",", $fields['purviews']) : array();

            // 组权限
            $groupids = db::select('groups')
                ->from(static::$table_config['user'])
                ->where('uid', '=', $this->uid)
                ->as_field()
                ->execute();
            $groupids = empty($groupids) ? null : explode(',', $groupids);

            $group_purviews = $this->get_group_purviews( $groupids );
            $purviews = array_merge($purviews, $group_purviews);
            $purviews = array_flip(array_flip($purviews));
            if ( in_array('*', $purviews) ) 
            {
                $purviews = '*';
            }
            else 
            {
                $purviews = implode(",", $purviews);
            }
            cache::set($cache_key, $purviews);
            $purviews = !empty($purviews) ? $purviews : '';
        }

        $user = $this->get_user($this->uid);
        // 如果是伪密码登陆，只显示正常的栏目
        if ( isset($user['seclogin']) && $user['seclogin'] )
        {
            $config = config::instance('config')->get('security');
            $purviews = $config['seclogin'];
        }

        return $purviews;
    }
    /**
     * 注销登录
     */
    public function logout()
    {
        $uid = kali::$auth->uid;
        mod_session::del_user_session($uid);

        // 删除用户缓存数据
        $this->del_cache();

        // 清空SESSION
        if( !empty($_SESSION[self::$auth_hand.'_uid']) ) 
        {
            $_SESSION[self::$auth_hand.'_uid'] = '';
            session_destroy();
        }

        // 删除COOKIE中的uid
        $this->_drop_cookie('uid');

        return true;
    }

    /**
     * 记录一下用户ID到session和cookie，用户ID和用户数据已经在上一步保存了
     * @parem $rows  用户信息
     * @parem $keeptime 登录状态保存时间
     * @return bool
     */
    public function auth_user( &$row )
    {
        if( !is_array( $row ) || !isset($row['uid']) )
        {
            return false;
        }

        $this->_put_logininfo($row);
        $this->uid = $row['uid'];
        return true;
    }

    /**
     * 保存登录信息
     * 
     * @param mixed $row        登陆信息
     * @param int $keeptime     保持时间，默认一天
     * @return void
     */
    protected function _put_logininfo( &$row )
    {
        $uid = $row['uid'];
        if(self::$config['auttype']=='session')
        {
            $_SESSION[static::$auth_hand.'_uid'] = $uid;
        }

        $this->_put_cookie('uid', $uid, self::$cookie_config['expire']);
    }

    /**
     * 保存一个cookie值
     * $key, $value, $keeptime
     */
    protected function _put_cookie( $key, $value, $keeptime = 0, $encode = true )
    {
        $keeptime = $keeptime==0 ? null : time()+$keeptime;
        $key = static::$auth_hand.'_'.$key;
        setcookie($key, $value, $keeptime, static::$cookie_config['path'], static::$cookie_config['domain']);
        // 数据调用是调用这个加密的数据，然后解密
        if ( $encode )
        {
            $key = $key.'_call';
            $value = substr(md5(static::$cookie_config['pwd'].$value), 0, 24);
            setcookie($key, $value, $keeptime, static::$cookie_config['path'], static::$cookie_config['domain']);
        }
    }

    /**
     * 删除cookie值
     *
     * @parem $key
     */
    protected function _drop_cookie( $key, $encode = true )
    {
        $key = static::$auth_hand.'_'.$key;
        setcookie($key, '', time()-3600, static::$cookie_config['path'], static::$cookie_config['domain']);
        if($encode)
        {
            setcookie($key.'_call', '', time()-3600, static::$cookie_config['path'], static::$cookie_config['domain']);
        }
    }

    /**
     * 获得经过加密对比的cookie值
     *
     * @parem $key
     */
    public function get_cookie( $key, $encode = true )
    {
        $key = static::$auth_hand.'_'.$key;
        if( !isset($_COOKIE[$key]) )
        {
            return '';
        }

        // 加密的话先对比一下加密值是否一样，是才返回真正结果
        if($encode)
        {
            if( !isset($_COOKIE[$key.'_call']) ) 
            {
                return '';
            }

            $epwd = substr( md5(static::$cookie_config['pwd'].$_COOKIE[$key]), 0, 24 );
            return ($_COOKIE[$key.'_call'] != $epwd ) ? '' : $_COOKIE[$key];
        }
        else
        {
            return $_COOKIE[$key];
        }
    }

    /**
     * 检查密码
     * 
     * @param string $password          明文
     * @param string $hash_password     密文
     * @return bool
     */
    public static function check_password( $password, $hash_password )
    {
        return password_verify($password , $hash_password);
    }

    /**
     * 会员密码加密方式接口（默认是 md5）
     */
    public static function password_hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);  // 使用BCRYPT算法加密密码
        //return md5($password);
    }

    /**
     * 获取用户具体信息
     *
     * @return mix array|false
     */
    public function get_user( $account = null, $ftype = 'uid', $use_cache = true )
    {
        if ( $account === null ) 
        {
            $account = $this->uid;
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

        $user = false;
        if ( $use_cache ) 
        {
            // 缓存读取
            $user = $this->get_cache($uid);
        }

        // 源数据
        if( $user === false )
        {
            // 读取用户数据
            $user = db::select(static::$fields)
                ->from(static::$table_config['user'])
                ->where('uid', '=', $uid)
                ->as_row()
                ->execute();

            // 用户存在
            if ( $user ) 
            {
                $user['utma'] = md5(req::ip().'-'.req::user_agent());
                // 剔除敏感信息
                //unset($user['password'], 
                    //$user['onetime_password'], 
                    //$user['fake_password'], 
                    //$user['status']);
                $this->set_cache($user, $uid);
                return $user;
            }
            return false;
        }
        else
        {
            return $user;
        }
    }

    /**
     * @param string $user
     * @return bool
     */
    public function save_user($data)
    {
        // 不要保存明文密码
        if ( isset($data['password']) && $data['password'] ) 
        {
            $data['password'] = static::password_hash($data['password']);
        }
        // 伪造密码
        if ( isset($data['fake_password']) && $data['fake_password'] ) 
        {
            $data['fake_password'] = static::password_hash($data['fake_password']);
        }
        // 一次性密码
        if ( isset($data['onetime_password']) && $data['onetime_password'] ) 
        {
            $data['onetime_password'] = static::password_hash($data['onetime_password']);
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
     * 设置用户缓存
     *
     * @return bool
     */
    public function get_cache( $uid = null )
    {
        $uid = $uid == null ? $this->uid : $uid;
        return cache::get($this->_cache_prefix.'-'.$uid);
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
        cache::set($this->_cache_prefix.'-'.$uid, $user);
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
        cache::del($this->_cache_prefix.'-'.$uid);
        // 删除用户权限信息
        cache::del($this->_cache_prefix.'_purview_mods'.'-'.$uid);
    }

    public function get_group_purviews( array $groupids = null )
    {
        if ( !$groupids ) 
        {
            return array();
        }

        $groups = db::select('purviews')
            ->from(static::$table_config['user_group'])
            ->where('id', 'IN', $groupids)
            ->execute();
        $purviews = array();
        foreach ($groups as $group) 
        {
            $tmp_purviews = empty($group['purviews']) ? array() : explode(",", $group['purviews']);
            $purviews = array_merge($purviews, $tmp_purviews);
        }

        // 移除数组中重复的值
        $purviews = array_unique($purviews);
        return $purviews;
    }

    public function get_groupname( array $groupids = null )
    {
        if ( !$groupids ) 
        {
            return '';
        }

        $rows = db::select('name')
            ->from(static::$table_config['user_group'])
            ->where('id', 'in', $groupids)
            ->execute();
        $groups = [];
        if ($rows) 
        {
            foreach ($rows as $row) 
            {
                $groups[] = $row['name'];
            }
        }
        $groupname = implode(",", $groups);
        return $groupname;
    }

    /**
     *  保存管理日志
     *  @parem $username 管理员登录id 
     *  @parem $msg 具体消息（如有引号，无需自行转义）
     *  @return bool
     */
    public function save_admin_log($msg)
    {
        $msg = addslashes( $msg );
        $url = '?ct='.kali::$ct.'&ac='.kali::$ac;
        foreach(req::$forms as $k => $v)
        {
            if( preg_match('/pwd|password|sign|cert/', $k) || $k=='ct' || $k=='ac' ) 
            {
                continue;
            }
            $nstr = "&{$k}=".(is_array($v) ? 'array()' : $v);
            if( strlen($url.$nstr) < 100 ) 
            {
                $url .= $nstr;
            } 
            else 
            {
                break;
            }
        }

        $uid = kali::$auth->uid;
        $user = $this->get_user($uid);
        $do_url     = addslashes( $url );
        $do_time    = time();
        $do_ip      = req::ip();
        $do_country = req::country();
        $rs = db::insert(static::$table_config['user_oplog'])
            ->set(array(
                'session_id' => session_id(),
                'uid'        => $uid,
                'username'   => $user['username'],
                'msg'        => $msg,
                'do_time'    => $do_time,
                'do_ip'      => $do_ip,
                'do_country' => $do_country,
                'do_url'     => $do_url,
            ))->execute();
        return $rs;
    }

    /**
     * 检测用户24小时内连续输错密码次数是否已经超过
     * @return bool 超过返回true, 正常状态返回false
     */
    public function get_login_error24( $username, $logintype = 'cli_hash' )
    {
        $error_num = 3;
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
            ->where($logintype, '=', $loginval)
            ->where('logintime', '>', $starttime)
            ->order_by('logintime', 'DESC')
            ->limit($error_num)
            ->execute();

        if( $rows === null || count($rows) < $error_num )
        {
            return false;
        }
        foreach ($rows as $row) 
        {
            // 最近3条有一条登录成功就不属于禁用账号
            if( $row['loginsta'] > 0 ) 
            {
                return false;
            }
        }
        return true;
    }

    public function del_login_error24()
    {
        $error_num = 3;
        $starttime = strtotime( date('Y-m-d 00:00:00', time()) );
        $rows = db::select('id, loginsta')
            ->from(static::$table_config['user_login'])
            ->where('uid', '=', $this->uid)
            ->where('logintime', '>', $starttime)
            ->order_by('logintime', 'DESC')
            ->limit($error_num)
            ->execute();

        if( $rows === null || count($rows) < $error_num )
        {
            return false;
        }

        foreach ($rows as $row) 
        {
            // 最近3条有一条登录成功就不属于禁用账号
            if( $row['loginsta'] > 0 ) 
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
     * 保存历史登录记录
     */
    public function save_login_history(&$row, $loginsta)
    {
        // 重新生成一下SESSION ID，这样每次登陆的SESSION ID都不同
        session_regenerate_id();

        $ltime = time();
        $loginip  = req::ip();
        $cli_hash = md5($row['username'].'-'.$loginip);
        $row['uid'] = isset($row['uid']) ? $row['uid'] : 0;

        if ( !empty($row['uid'])) 
        {
            db::update(static::$table_config['user'])
                ->set([
                    'session_id'   => session_id(), // 保存session_id到数据库，用于后台随时踢出
                    'logintime'    => $ltime,
                    'loginip'      => $loginip,
                ])
                ->where('uid', $row['uid'])
                ->execute();
        }

        db::insert(static::$table_config['user_login'])
            ->set([
                'session_id'   => session_id(),
                'uid'          => $row['uid'],
                'username'     => $row['username'],
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
     * 获得用户上次登录时间和ip
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
        if( isset($datas[1]) )
        {
            return $datas[1];
        } 
        else 
        {
            return array('loginip'=>'', 'logincountry'=>'-', 'logintime'=>0);
        }
    }
}

