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
     * @return	string
     */
    public static function encode(string $value, string $key) :string
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        cls_aes::instance()->set_key(substr($key, 0, 16));
        cls_aes::instance()->set_iv(substr($key, 16, 16));
        $value = cls_aes::instance()->encrypt($value);

        return $value;
    }

    /**
     * decode
     * 
     * @param	string	$value
     * @param	string	$key
     * @return	string
     */
    public static function decode(string $value, string $key) :string
    {
        if ( strlen($key) != 32 ) 
        {
            $msg = '加密Key必须满足32位';
            log::error($msg, __method__);
            throw new Exception($msg);
        }

        cls_aes::instance()->set_key(substr($key, 0, 16));
        cls_aes::instance()->set_iv(substr($key, 16, 16));
        $value = trim(cls_aes::instance()->decrypt($value));
        
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
