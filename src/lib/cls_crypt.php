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

use kaliphp\log;
use Exception;

class cls_crypt
{
    /**
     * encode
     * 
     * @param	string	$value
     * @param	string	$key
     * @param	bool	$need_base64
     * @return	string
     */
    public static function encode(string $value, string $key, bool $is_gzip = false, bool $is_base64 = false): string
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        // 压缩
        if ($is_gzip)
        {
            $value = zlib_encode($value, ZLIB_ENCODING_DEFLATE, 9);
        }

        // 加密
        cls_aes::instance()->set_key(substr($key, 0, 16));
        cls_aes::instance()->set_iv(substr($key, 16, 16));
        $value = cls_aes::instance()->encrypt($value);

        // BASE64
        if ($is_base64)
        {
            $value = self::safe_b64encode($value);
        }

        return $value;
    }

    /**
     * decode
     * 
     * @param	string	$value
     * @param	string	$key
     * @param	bool	$need_base64
     * @return	string
     */
    public static function decode(string $value, string $key, bool $is_gzip = false, bool $is_base64 = false): string
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        // BASE64
        if ($is_base64)
        {
            $value = self::safe_b64decode($value);
        }

        // 解密
        cls_aes::instance()->set_key(substr($key, 0, 16));
        cls_aes::instance()->set_iv(substr($key, 16, 16));
        $value = cls_aes::instance()->decrypt($value);

        // 解压
        if ($is_gzip) 
        {
            if (@zlib_decode($value) === false)
            {
                return '';
            }

            $value = zlib_decode($value);
        }

        return $value;
    }

    /**
     * generate a URI safe base64 encoded string
     *
     * @param	string	$value
     * @return	string
     */
    protected static function safe_b64encode($value)
    {
        $value = base64_encode($value);
        return str_replace(['+', '/', '='], ['-', '_', ''], $value);
    }

    /**
     * decode a URI safe base64 encoded string
     *
     * @param	string	$value
     * @return	string
     */
    protected static function safe_b64decode($value)
    {
        $value = str_replace(['-', '_'], ['+', '/'], $value);
        $mod4 = strlen($value) % 4;
        if ($mod4)
        {
            $value .= substr('====', $mod4);
        }
        return base64_decode($value);
    }

    protected static function bin2hex($value)
    {
        return @bin2hex($value);
    }

    protected static function hex2bin($value)
    {
        return @pack('H*', $value);
    }
}
