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

namespace kaliphp;

use kaliphp\errorhandler;
use kaliphp\lib\cls_crypt;

/**
 * response
 *
 * @version 2.0
 */
class resp
{
    public static function error(int $code = -1, string $msg = 'faild')
    {
        self::response($code, [], $msg);
    }

    public static function success(array $data = [], string $msg = 'successful')
    {
        self::response(0, $data, $msg);
    }

    public static function response_error(int $code = -1, string $msg = 'faild')
    {
        self::response($code, [], $msg);
    }

    public static function response_success(array $data = [], string $msg = 'successful')
    {
        self::response(0, $data, $msg);
    }

    public static function response(int $code = 0, array $data = [], string $msg = 'successful')
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = [
            'code'      => $code,
            'msg'       => $msg,
            'data'      => $data,
            'timestamp' => FRAME_TIMESTAMP,
        ];

        if (defined('SYS_DEBUG') && SYS_DEBUG)
        {
            $data['trace']  = errorhandler::$_debug_error_msg;
            $data['sqlnum'] = count(db::$queries);
            $data['sqls']   = db::$queries;
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        // var_dump(req::get_encrypt_key(), req::get_use_encrypt(), req::get_use_base64(), req::get_use_compress()); exit;

        if (req::get_use_encrypt()) 
        {
            $json = cls_crypt::encode(
                $json, 
                req::get_encrypt_key(), 
                req::get_use_compress(),
                req::get_use_base64()
            );
        }

        exit($json);
    }
}
