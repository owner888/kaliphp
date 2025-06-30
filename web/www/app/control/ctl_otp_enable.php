<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_google_auth;


/**
 * MFA认证
 *
 * @version $Id$
 */
class ctl_otp_enable
{
    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
    }

    public function authentication()
    {
        $err_msg = '';
        if ( req::method() == 'POST' ) 
        {
            $username = req::item("username");
            $password = req::item("password");

            try
            {
                $user = kali::$auth->check_user( $username, $password );
                if ( $user )
                {
                    $jumpurl = '?ct=otp_enable&ac=install_app';
                    exit(header("location: {$jumpurl}"));
                    //cls_msgbox::show('安全设置', '验证成功，正在重定向APP安装页面', $jumpurl);
                }
            }
            catch (Exception $e)
            {
                $err_msg = $e->getMessage();
            }
        }

        tpl::assign('err_msg', $err_msg); 
        tpl::assign('username', $_SESSION['otp_username']); 
        tpl::display('otp_enable.authentication.tpl');
    }

    public function install_app()
    {
        tpl::assign('username', $_SESSION['otp_username']); 
        tpl::display('otp_enable.install_app.tpl');
    }

    public function bind()
    {
        $err_msg = '';
        $username = $_SESSION['otp_username'];

        if ( req::method() == 'POST' ) 
        {
            $otp_code = req::item("otp_code");
            $secret = $_SESSION['otp_authcode'];
            $ret = cls_google_auth::verify_code($secret, $otp_code);    // 2 = 2*30sec clock tolerance
            if ( $ret ) 
            {
                db::update('#PB#_admin')->set(array(
                    'otp_authcode' => $secret
                ))
                ->where('username', $username)
                ->execute();
                $jumpurl = '?ct=index&ac=login';
                //exit(header("location: {$jumpurl}"));
                cls_msgbox::show('安全设置', 'MFA 绑定成功，返回到登录页面', $jumpurl);
            }
            else 
            {
                $err_msg = 'MFA码认证失败';
                //cls_msgbox::show('安全设置', '绑定失败，请把手机设置为24小时制后再次尝试', -1);
            }
        }

        $secret = $_SESSION['otp_authcode'];
        $qrcode_url = cls_google_auth::get_qrcode_url($username, $secret, kali::$app_title);
        tpl::assign('imagedata', 'data:image/png;base64,'.base64_encode(file_get_contents($qrcode_url))); 
        tpl::assign('err_msg', $err_msg); 
        tpl::assign('username', $username); 
        tpl::display('otp_enable.bind.tpl');
    }
}
