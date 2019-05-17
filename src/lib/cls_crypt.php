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
use kaliphp\log;
use Exception;

class cls_crypt
{
    /**
     * encode
     * 
     * @param mixed $value
     * @param string $key
     * @param string $type kali|aes
     * @return void
     */
    public static function encode($value, $key = '', $type = 'aes')
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        if ( $type == 'kali' && function_exists('kali_encrypt') ) 
        {
            $value = kali_encrypt($value, $key);
        }
        else 
        {
            cls_aes::instance()->set_key(substr($key, 0, 16));
            cls_aes::instance()->set_iv(substr($key, 16, 16));
            $value = cls_aes::instance()->encrypt($value);
        }

        $value = self::safe_b64encode($value);
        return $value;
    }

    /**
     * decode
     * 
     * @param mixed $value
     * @param string $key
     * @param string $type kali|aes
     * @return void
     */
    public static function decode($value, $key = '', $type = 'aes')
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        $value = self::safe_b64decode($value);

        if ( $type == 'kali' && function_exists('kali_encrypt') ) 
        {
            $value = kali_decrypt($value, $key);
        }
        else 
        {
            cls_aes::instance()->set_key(substr($key, 0, 16));
            cls_aes::instance()->set_iv(substr($key, 16, 16));
            $value = cls_aes::instance()->decrypt($value);
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
        $value = str_replace(array('+', '/', '='), array('-', '_', ''), $value);
        return $value;
    }

    /**
     * decode a URI safe base64 encoded string
     *
     * @param	string	$value
     * @return	string
     */
    protected static function safe_b64decode($value)
    {
        $value = str_replace(array('-', '_'), array('+', '/'), $value);
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
