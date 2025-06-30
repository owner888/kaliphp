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

use kaliphp\config;
use kaliphp\log;

/** 
 * 字符串类
 *
 * @version $Id$  
 */
class cls_string
{
    /**
     * 发送邮件
     *
     * @param  [type] $email   [description]
     * @param  [type] $subject [description]
     * @param  [type] $body    [description]
     * @return [type]          [description]
     */
    public static function send_email($email, $subject, $body)
    {
        // 初始化邮箱类
        $mail              = new \PHPMailer\PHPMailer\PHPMailer(true);
        $config            = config::instance('config')->get('send_email');
        $config['port']    = empty($config['port']) ? 465 : $config['port'];
        $config['secure']  = empty($config['secure']) ? 'ssl' : $config['secure'];
        $config['is_html'] = isset($config['is_html']) ? $config['is_html'] : true;
        $config['auth']    = isset($config['auth']) ? $config['auth'] : true;
        try
        {
            $mail->CharSet   = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = $config['auth']; // gmail 此项必须是 true
            $mail->Username   = $config['user'];
            $mail->Password   = $config['pass'];
            $mail->SMTPSecure = $config['secure']; // ssl 或 tls (gmail)
            $mail->Port       = $config['port'];   // ssl: 465, tls: 587

            $mail->setFrom($config['user'], $config['name']);
            $mail->addAddress($email);

            $mail->isHTML($config['is_html']);
            $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $mail->Body    = $body;
            if ($config['is_html'])
            {
                $mail->AltBody = strip_tags($body);
            }
            $result = $mail->send();
            return $result;
        }
        catch (\PHPMailer\PHPMailer\Exception $e)
        {
            log::error($mail->ErrorInfo, __METHOD__);
            return false;
        }
    }
}
