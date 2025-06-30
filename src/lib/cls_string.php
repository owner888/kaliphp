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

/** 
 * 字符串类
 *
 * @version $Id$  
 */
class cls_string
{
    // 删除空格，替换中文逗号
    public static function trim_all(string $str)
    {
        $search = ["，", " ","　","\t","\n","\r", "f"];
        $replace = [",","","","","","", ""];
        return str_replace($search, $replace, $str); 
    }

    public static function new_addslashes($string)
    {
        if (is_array($string))
        {
            foreach ($string as $key => $val)
            {
                $string[$key] = self::new_addslashes($val);
            }
        }
        else
        {
            $string = addslashes($string);
        }

        return $string;
    }

    public static function new_stripslashes($data)
    {
        if (is_array($data))
        {
            foreach ($data as $k => $v)
            {
                $data[$k] = self::new_stripslashes($data[$k]);
            }
        }
        else
        {
            // 同时转义双,单引号
            $data = stripslashes($data);
        }

        return $data;
    }

    public static function htmlentities($data)
    {
        if (is_array($data))
        {
            foreach ($data as $k => $v)
            {
                $data[$k] = self::htmlentities($data[$k]);
            }
        }
        else
        {
            // 同时转义双,单引号
            $data = htmlspecialchars(trim($data), ENT_QUOTES);
        }

        return $data;
    }

    /**
     * function htmlentities_decode(): 处理转义字符串
     *
     * @scope public
     * @param string|array  $data        : 需要转义的内容
     * @return string|array data string
     *                         ex. cls_string::mask_string( "12345678",3)  : 123***78
     *                         ex. cls_string::mask_string( "12345678",-3) : 12*****8
     */
    public static function htmlentities_decode($data)
    {
        if (is_array($data))
        {
            foreach ($data as $k => $v)
            {
                $data[$k] = self::htmlentities_decode($data[$k]);
            }
        }
        else
        {
            // 同时转义双,单引号
            $data = htmlspecialchars_decode(htmlspecialchars_decode(trim($data), ENT_QUOTES));
        }

        return $data;
    }

    /**
     * 递归把 bigint 转为 string
     *
     * @param array $data     原始数组
     * @param integer $length int长度
     * @return array
     */
    public static function bigint_to_string(array $data, $length = 15)
    {
        foreach ($data as &$val)
        {
            if (is_numeric($val) && strlen($val) >= $length)
            {
                $val = (string)$val;
            }
            else if (is_array($val))
            {
                foreach ($val as $k => $v)
                {
                    if (is_array($v))
                    {
                        $val[$k] = static::bigint_to_string($v, $length);
                    }
                    else if (is_numeric($v) && strlen($v) >= $length)
                    {
                        $val[$k] = (string)$v;
                    }
                }
            }
        }

        unset($val);
        return $data;
    }

    /**
     * function mask_string(): Mask a string for security.
     *
     * @scope public
     * @param string $s        : input string, >2 characters long string
     * @param integer $masknum : the number of characters in the middle of a string to be masked,
     *                         if masknum is negative, the returned string will leave abs(masknum) characters in
     *                         both end untouched.
     * @return string masked string
     *                         ex. cls_string::mask_string( "12345678",3)  : 123***78
     *                         ex. cls_string::mask_string( "12345678",-3) : 12*****8
     */
    public static function mask_string($s, $masknum = 3)
    {
        $len = strlen($s);
        if ($masknum < 0) $masknum = $len + $masknum;
        if ($len < 3) return $s;
        else if ($len < $masknum + 1) return substr($s, 0, 1) . str_repeat('*', $len - 2) . substr($s, -1);
        $right = ($len - $masknum) >> 1;
        $left  = $len - $right - $masknum;
        return substr($s, 0, $left) . str_repeat('*', $len - $right - $left) . substr($s, -$right);
    }
}
