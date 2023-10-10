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

namespace kaliphp;

use kaliphp\lib\cls_crypt;

/**
 * response
 *
 * @version 2.0
 */
class resp
{
    private static $_encrypt = false;  // 是否加解密
    private static $_encrypt_key = ''; // 加解密 KEY

    public static $config = [];

    public static function set_encrypt($encrypt = false)
    {
        self::$_encrypt = $encrypt;
    }

    public static function set_encrypt_key($encrypt_key = '')
    {
        self::$_encrypt_key = $encrypt_key;
    }

    public static function response_error($code = -1, $msg = 'faild')
    {
        self::response($code, [], $msg);
    }

    public static function response($code = 0, $data = [], $msg = 'successful')
    {
        header('Content-Type: application/json; charset=utf-8');

        // php7.1 json_encode float 精度会溢出
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('serialize_precision', -1);
        }

        $data = [
            'code'      => (int) $code,
            'msg'       => (string) $msg,
            'data'      => $data,
            'timestamp' => KALI_TIMESTAMP,
        ];

        // if(defined('SYS_DEBUG') && SYS_DEBUG)
        // {
        //     $data['trace']  = errorhandler::$_debug_error_msg;
        //     $data['sqlnum'] = count(db::$queries);
        //     $data['sqls'] = db::$queries;
        // }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        // //返回结果记录到日志中去
        // //记录日志比较危险剔除一些字段
        // $tmp_post['ct']          = kali::$ct;
        // $tmp_post['ac']          = kali::$ac;
        // $tmp_post['device_info'] = $this->device_info;
        // $tmp_post['post']        = req::$posts;
        // $tmp_post['response']    = $data;
        //
        // // if (!in_array($tmp_post['ct'], ['message']))
        // if ( !empty($tmp_post['ct']) )
        // {
        //     util::shutdown_function(['common\model\pub_mod_app', 'api_request_log'], [$tmp_post]);
        // }

        // var_dump($data);exit();
        // if( is_object(kali::$auth) && (false != pub_func::get_value(kali::$auth, 'aes_key')) )
        // {
        //     // 方便内网调试
        //     if ( in_array(SYS_ENV, ['dev', 'pre']) && !empty($_SERVER['HTTP_DEBUG']) )
        //     {
        //         echo "\n ouput:";
        //         echo $json . "\n";
        //     }
        //
        //     $json = cls_crypt::encode($json, kali::$auth->aes_key);
        // }

        if (self::$_encrypt) 
        {
            $json = cls_crypt::encode($json, self::$_encrypt_key);
        }

        exit($json);
    }
}
