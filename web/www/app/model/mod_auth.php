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

namespace model;

use kaliphp\db;
use kaliphp\log;
use kaliphp\req;
use kaliphp\util;
use kaliphp\config;
use kaliphp\cache;
use kaliphp\session;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_validate;
use kaliphp\lib\cls_auth;
use Exception;

/**
 * 管理员权限控制类
 *
 * @version $Id$
 */
class mod_auth extends cls_auth
{
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
        'username', 
        'password', 
        'realname',
        'avatar',
        'email',
        'groups',
        'session_id',
        'session_expire',
        'status',
        'is_first_login',
        'fake_password',
        'onetime_password',
        'otp_authcode' 
    ];

    public static function _init()
    {
    }

    public static function auth( $ct, $ac )
    {
        if ( static::$config['auttype'] == 'session' )
        {
            $uid = isset($_SESSION[static::$auth_hand.'_uid']) 
                ? $_SESSION[static::$auth_hand.'_uid'] 
                : 0;
        }
        else 
        {
            $token = req::item('token', '');
            $uid   = static::get_uid_by_token( $token );
            $uid   = $uid ? $uid : 0;
        }

        $auth = static::instance( $uid );
        $auth->check_purview( $ct, $ac, 1 );
        $auth->user = $auth->get_user();

        $safe_actions = ['logout', 'login', 'authentication'];
        if ( !in_array($ac, $safe_actions) ) 
        {
            if(  //登陆IP不在白名单，禁止操作
                !empty($auth->user['safe_ips']) && 
                !in_array(IP, explode(',', str_replace('，', ',', $auth->user['safe_ips']))) 
            ) 
            {
                $msg = "IP不在白名单内,无法操作";
                if ( req::is_ajax() ) 
                {
                    util::return_json(array(
                        'code' => -10100,
                        'msg'  => $msg
                    ));
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
     * 构造函数，根据池的初始化检测用户登录信息
     *
     * @parem $uid  用户ID
     */
    public function __construct( $uid = 0 )
    {
        $this->uid = $uid;

        // 如果用户ID存在，获取登录后可访问的 控制器-方法
        if ( $this->uid ) 
        {
            $purviews = $this->get_purviews();

            if ( empty($purviews)) 
            {
                static::$config['private'] = '';
            }
            elseif ( $purviews == '*' ) 
            {
                static::$config['private'] = '*';
            }
            else 
            {
                static::$config['private'] = [];
                $purviews = explode(",", $purviews);
                foreach ($purviews as $purview) 
                {
                    $mods = explode('-', $purview);
                    static::$config['private'][$mods[0]][] = $mods[1];
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
    public function check_user( string $account, string $loginpwd, int $remember = 0 )
    {
        if( $account == '' || $loginpwd == '' )
        {
            throw new Exception('请输入会员名密码');
        }

        // 检测用户名合法性.
        $ftype = 'username';
        if( cls_validate::instance()->email($account) )
        {
            $ftype = 'email';
        }
        else if( !cls_validate::instance()->username($account) )
        {
            throw new Exception('会员名格式不合法！');
        }

        // 同一IP使用某帐号连续错误次数检测
        if( $this->get_login_error24( $account ) )
        {
            throw new Exception('连续登录失败超过3次，暂时禁止登录！');
        }

        // 读取用户ID
        $user = $this->get_user( $account, $ftype, false );

        // 存在用户数据
        if( is_array($user) )
        {
            if ( !$user['status'] ) 
            {
                throw new Exception ('用户禁用！');
            }

            // 自动登陆
            $user['remember'] = $remember;
            // 秘密登陆
            $user['seclogin'] = 0;

            $loginsta = false;
            // 正常密码，正确生成会话信息
            if( static::check_password($loginpwd, $user['password']) )
            {
                $loginsta = true;
            }

            // 登录成功
            if ( $loginsta ) 
            {
                $filelink =  config::instance('upload')->get('filelink');
                $user['avatar'] = $filelink.'/uploads/avatar/'.$user['avatar'].'.jpg';

                if ( static::$config['auttype'] == 'session' )
                {
                    // 重新生成一下SESSION ID，这样每次登陆的SESSION ID都不同
                    session_regenerate_id();
                    $session_id = session_id();
                }
                else 
                {
                    // 增加token，用于支持 app 登录
                    $session_id = util::random('web');
                    static::bind_token_uid( $session_id, $user['uid'], $user['session_expire'] );
                }

                // 更新缓存里面的信息
                $user['session_id'] = $session_id;
                $this->set_cache($user, $user['uid']);
                $this->save_login_history($user, 1, $session_id);
                return $user;
            }
            //密码错误，保存登录记录
            else
            {
                $this->save_login_history($user, 0);
                throw new Exception ('用户名或密码无效');
            }
        }
        //不存在用户数据时不进行任何操作
        else
        {
            $user['username'] = $account;
            $this->save_login_history($user, 0);
            throw new Exception ('用户名或密码无效');
        }
    }

    /**
     * 获取用户具体信息
     *
     * @return mix array|false
     */
    public function get_user( string $account = null, string $ftype = 'uid', bool $use_cache = true )
    {
        if ( $account === null ) 
        {
            $account = $this->uid;
        }
        //如果是token先获取token对应的uid
        else if ( 'token' == $ftype ) 
        {
            $account = static::get_uid_by_token($account);
            $ftype   = 'uid';
            if ( !$account ) return false;
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
            $user = db::select(static::$table_fields)
                ->from(static::$table_config['user'])
                ->where('uid', '=', $uid)
                ->as_row()
                ->execute();

            // 用户存在
            if ( $user ) 
            {
                $user['utma'] = md5(req::ip().'-'.req::user_agent());
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
     * 保存用户信息到数据库表
     * 
     * @param array $data
     * @return void
     */
    public function save_user($data)
    {
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

        parent::save_user($data);
    }

    /**
     * 检测权限
     *
     * @parem $mod
     * @parem $action
     * @parem backtype 返回类型， 1--是由权限控制程序直接处理
     * @return int  对于没权限的用户会提示或跳转到 ct=index&ac=login
     */
    public function check_purview( string $mod, string $action, int $backtype = 1 )
    {
        // 未登录用户
        $rs = 0;

        // 获取检测应用池开放权限的模块
        $public_mod = isset(static::$config['public'][$mod]) ? static::$config['public'][$mod] : array();
        // 检测开放的控制器和方法
        if( !empty(static::$config['public']) && 
            ( static::$config['public']=='*' || in_array($action, $public_mod) || in_array('*', $public_mod) ) )
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
            $protected_mod = isset(static::$config['protected'][$mod]) ? static::$config['protected'][$mod] : array();

            if ( !empty(static::$config['protected']) && 
                ( static::$config['protected']=='*' || in_array('*', static::$config['protected']) || in_array($action, $protected_mod) || in_array('*', $protected_mod) ) )
            {
                $rs = 1;
            }
            else
            {
                //echo '<pre>';print_r(static::$config['private']);echo '</pre>';
                // 检测开放控制器和事件（即是登录用户允许访问的所有公共事件）
                $private_mod = isset(static::$config['private'][$mod]) ? static::$config['private'][$mod] : array();
                if ( !empty(static::$config['private']) && 
                    ( static::$config['private']=='*' || in_array($action, $private_mod) || in_array('*', $private_mod) ) )
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
                if ( req::is_ajax() ) 
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
                if ( req::is_ajax() ) 
                {
                    util::return_json(array(
                        'code' => -1,
                        'msg'  => '用户未登陆！'
                    ));
                }
                else 
                {
                    $jumpurl = static::$config['login_url'];
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
        $cache_key = static::$_cache_prefix.'_purview_mods'.'-'.$this->uid;
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
            if ( $purviews ) 
            {
                if ( in_array('*', $purviews) ) 
                {
                    $purviews = '*';
                }
                else 
                {
                    $purviews = implode(",", $purviews);
                }
                cache::set($cache_key, $purviews);
            }
            else 
            {
                $purviews = '';
            }
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
        $url = '?ct='.req::item('ct').'&ac='.req::item('ac');
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

        $uid = $this->uid;
        $user = $this->get_user($uid);
        $do_url     = $url;
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
    public function save_login_history( $row, $loginsta, $session_id = null )
    {
        $ltime = time();
        $loginip  = req::ip();
        $cli_hash = md5($row['username'].'-'.$loginip);
        $row['uid'] = isset($row['uid']) ? $row['uid'] : 0;

        if ( !empty($row['uid'])) 
        {
            db::update(static::$table_config['user'])
                ->set([
                    'session_id'   => $session_id, // 保存session_id到数据库，用于后台随时踢出
                    'logintime'    => $ltime,
                    'loginip'      => $loginip,
                ])
                ->where('uid', $row['uid'])
                ->execute();
        }

        db::insert(static::$table_config['user_login'])
            ->set([
                'session_id'   => $session_id,
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


