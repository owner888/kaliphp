<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\kali;
use kaliphp\util;
use kaliphp\cache;
use kaliphp\config;
use kaliphp\lib\cls_auth;
use kaliphp\lib\cls_menu;
use kaliphp\lib\cls_redis;
use kaliphp\lib\cls_crypt;
use kaliphp\lib\cls_notice;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_snowflake;
use kaliphp\lib\cls_redis_lock;
use kaliphp\lib\cls_securimage;
use kaliphp\lib\cls_google_auth;

class ctl_index
{
    public static $config = [];

    public static function _init()
    {   
        $security = config::instance('config')->get('security');
        self::$config = $security['validate'];
    }

    public function document()
    {
        tpl::assign('is_mobile', req::is_mobile());
        tpl::display('document/index.tpl');
    }

    /**
     * 主入口
     */
    public function index()
    {
        tpl::assign('menus', cls_menu::parse_menu());
        tpl::assign('user', kali::$auth->get_user());

        //// websocket
        //$conf = config::instance('config')->get('websocket');
        //$url_websocket = '';
        //if ( $conf['enable'] )
        //{
            //$key        = util::random('web');
            //$uid        = kali::$auth->uid;
            //$session_id = session_id();
            //$data = [
                //'key'        => $key,
                //'uid'        => $uid,
                //'session_id' => $session_id,
                //'ip'         => IP,
            //];
            //$api_key = config::instance()->get('api_key');
            //$data = json_encode($data);
            //$data = cls_crypt::encode($data, $api_key);
            //$websocket_url = "{$conf['scheme']}://{$conf['host']}:{$conf['port']}/json?data={$data}";
            //// key用于数据加解密
            //$websocket_key = $key;
        //}

        //tpl::assign('websocket_url', $websocket_url);
        //tpl::assign('websocket_key', $websocket_key);
        tpl::display('index.tpl');
    }

    /**
     * 用户登录
     */
    public function login()
    {
        $username = req::item('username', '');
        $password = req::item('password', '');
        $validate = req::item('validate', '');
        $remember = req::item('remember', 0);

        $gourl = req::item('gourl', '');
        $errmsg = '';

        if ( req::method() == 'POST' ) 
        {
            try
            {
                // 如果开启了图片验证码验证
                if ( self::$config['image_code'] ) 
                {
                    $vdimg = new cls_securimage();
                    if ( empty($validate) || !$vdimg->check($validate) )
                    {
                        throw new \Exception('请输入正确的验证码！');
                    }
                }

                if ( $user = kali::$auth->check_user( $username, $password, $remember ) )
                {
                    if ( $user['is_first_login'] ) 
                    {
                        $_SESSION['uid'] = $user['uid'];
                        $jumpurl = '?ct=index&ac=reset_pwd';
                        exit(header("location: {$jumpurl}"));
                    }
                    // 启动强制MFA认证 并且 用户未绑定，进行绑定流程
                    elseif ( self::$config['mfa_code'] && empty($user['otp_authcode']) ) 
                    {
                        $secret = cls_google_auth::create_secret();
                        $_SESSION['otp_username'] = $username;
                        $_SESSION['otp_authcode'] = $secret;

                        $jumpurl = '?ct=otp_enable&ac=authentication';
                        exit(header("location: {$jumpurl}"));
                    }
                    else 
                    {
                        if ( self::$config['mfa_code'] ) 
                        {
                            $_SESSION['otp_uid']      = $user['uid'];
                            $_SESSION['otp_remember'] = $remember;
                            $_SESSION['otp_username'] = $username;
                            $_SESSION['otp_authcode'] = $user['otp_authcode'];
                            $jumpurl = '?ct=index&ac=login_otp';
                            exit(header("location: {$jumpurl}"));
                        }
                        else 
                        {
                            // 保存用户ID到COOKIE和SESSION
                            kali::$auth->auth_user($user, $remember);
                            $jumpurl = empty($gourl) ? '?ct=index' : $gourl;
                            cls_msgbox::show('成功登录', '成功登录，正在重定向你访问的页面', $jumpurl);
                        }
                    }
                }
            }
            catch ( \Exception $e )
            {
                $errmsg = $e->getMessage();
            }
        }

        tpl::assign('username', $username );
        tpl::assign('password', $password );
        tpl::assign('remember', $remember );
        tpl::assign('errmsg', $errmsg );
        tpl::assign('image_code', self::$config['image_code'] );
        tpl::assign('third_login', self::$config['third_login'] );
        tpl::display('login.tpl');
    }

    /**
     * 退出
     */
    public function logout()
    {
        kali::$auth->logout();
        cls_msgbox::show('注销登录', '成功退出登录！', './');
        exit();
    }

    public function login_otp()
    {
        if ( empty($_SESSION)) 
        {
            exit(header("location: ?ct=index&ac=login"));
        }

        $errmsg = '';
        if ( req::method() == 'POST' ) 
        {
            $otp_code = req::item("otp_code");
            $secret   = $_SESSION['otp_authcode'];
            $uid      = $_SESSION['otp_uid'];
            $remember = $_SESSION['otp_remember'];

            $ret = cls_google_auth::verify_code($secret, $otp_code);    // 2 = 2*30sec clock tolerance
            if ( $ret ) 
            {
                $user = kali::$auth::instance($uid)->get_user();
                $user['remember'] = $remember;
                kali::$auth->auth_user( $user );
                $jumpurl = empty($gourl) ? '?ct=index' : $gourl;
                cls_msgbox::show('成功登录', '成功登录，正在重定向你访问的页面', $jumpurl);
            }
            else 
            {
                $errmsg = '* MFA码认证失败';
            }
        }

        tpl::assign('errmsg', $errmsg );
        tpl::display('login.tpl');
    }

    public function reset_pwd()
    {
        $password = req::item("password");
        $confpass = req::item("confpass");

        if ( empty($_SESSION) || empty($_SESSION['uid']) ) 
        {
            // 您尚未登录
            // 由于没有任何活动，您的会话已结束。请尝试重新登录。
            exit(header("location: ?ct=index&ac=login"));
        }

        $uid = $_SESSION['uid'];

        $errmsg = '';
        if ( req::method() == 'POST' ) 
        {
            $user = kali::$auth::instance($uid)->get_user();
            try
            {
                if ( $password != $confpass ) 
                {
                    throw new \Exception('这两个密码不一致，请重试');
                }

                // 使用了之前相同的密码
                if ( kali::$auth::password_hash($password) == $user['password'] ) 
                {
                    throw new \Exception('请使用您以前没有用过的密码重试');
                }

                $data = array(
                    'uid'            => $user['uid'],
                    'password'       => $password,
                    'is_first_login' => 0,
                );
                kali::$auth::instance($uid)->save_user($data);
                cls_msgbox::show('设置密码', '成功修改密码，请用您的新密码重新登录', '?ct=index&ac=login');
            }
            catch (\Exception $e)
            {
                $errmsg = $e->getMessage();
            }

        }

        tpl::assign('password', $password );
        tpl::assign('confpass', $confpass );
        tpl::assign('errmsg', $errmsg );
        tpl::display('login.tpl');
    }

    /**
     * 验证码图片
     */
    public function validate_image()
    {
        //$text_num=4, $im_x = 200, $im_y = 40, $scale = 5, $session_name='securimage_code_value'
        $vdimg = new cls_securimage(4, 94, 32, 3);
        $vdimg->show();
    }
    
    
    public function language_change()
    {
        $lang = req::item("lang");
        setcookie("language", $lang);
        $gourl = '?ct=index';
        cls_msgbox::show('成功切换语言', '成功切换到语言 '.$lang.'，正在重定向你访问的页面', $gourl);
    }

    /**
     * 生成默认权限系统的数据表
     * 实际使用中创建好表后应该删除此函数
     */
    public function db_infos()
    {
        if (SYS_DEBUG === true)
        {
            $type = req::item('type', '');
            mod_make_db_document::show( $type );
        }
        exit();
    }

    /**
     * 系统消息
     */
    public function adminmsg()
    {
        $addjob = req::item('addjob', '');
        if ($addjob=='del')
        {
            db::update('#PB#_admin_log')
                ->set(array(
                    'isread' => 1
                ))
                ->where('isalert', '=', 1)
                ->execute();
            exit('ok');
        }
        else
        {
            $row = db::select("count(*) As count")
                ->from('#PB#_admin_log')
                ->where('isalert', '=', 1)
                ->and_where('isread', '=', 0)
                ->as_row()
                ->execute();
            if ( is_array($row) && $row['count']>0 )
            {
                exit($row['count']);
            }
            else 
            {
                exit('false');
            }
        }
    }
}
