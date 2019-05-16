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
use Exception;

/**
 * 数据过滤类(这个类只对不符合类型的字符进行过滤，数据验证使用cls_validate.php类)
 * @author seatle<seatle@foxmail.com>
 * @version 1.0
 */
class cls_filter
{ 
    
    //过滤类型
    protected $_filter_types = array('int',         // 0-12位的数字(可包含-)
                                     'float',       // 小数
                                     'email',       // 邮箱
                                     'username',    // 用户名 \w 类型英文及任意中文字符
                                     'qq',          // 5-12位数字 (不匹配返回0)
                                     'mobile',      // 11位数字   (不匹配返回0)
                                     'ip',          // 用户ip
                                     'var',         // 变量名类型，即是 \w
                                     'keyword',     // 搜索关键字（对一些特殊字符进行过滤）
                                     'hash',        // 纯英文、数字组成的字符串
                                     'xss_clean',   // XSS过滤
                                     );
   
    /**
     * 过滤操作
     * @param $val   变量值
     * @param $type  当type为数字的时候，表示截取指定长度的字符
     * @param $throw_error 是否抛出异常(只对邮箱、用户名、qq、手机类型有效)，如果不抛出异常，会对无效的数据设置为空
     *                     (此值用户不直接使用，一般通过 req::$throw_error 进行设置)
     * @return void
     */
    public static function filter( &$val, $type = '', $throw_error = false )
    {
        // 没指定过滤类型或变量为数组，不处理
        if( $type=='' && is_array($val) )
        {
            return;
        }

        if ( is_array($type)) 
        {
            $val = call_user_func($type, $val);
            return;
        }

        $type = strtolower( $type );
        $val  = is_string($val) ? trim( $val ) : $val;
        switch( $type )
        {
            case 'int':
                $val = intval( $val );
                break;
            case 'float':
                $val = floatval( $val );
                break;
            case 'email':
                if( !self::_test_email($val) )
                {
                    if( strlen($val) > 0 && $throw_error ) {
                        self::_throw_errmsg( "Email不合法" );
                    } else {
                        $val = '';
                    }
                }
                break;
            case 'username':
                if( !self::_test_user_name($val) )
                {
                    if( $throw_error ) {
                        self::_throw_errmsg( "用户名不合法" );
                    } else {
                        $val = '';
                    }
                }
                break;
            case 'qq':
                $val = preg_replace("/[^0-9]/", '', $val);
                if( strlen($val) < 5 )
                {
                    if( $val > 0 && $throw_error ) {
                        self::_throw_errmsg( "QQ号码不合法" );
                    } else {
                        $val = '';
                    }
                }
                break;
            case 'mobile':
                $val = preg_replace("/[^0-9]/", '', $val);
                if( !preg_match("/1[3-9]{10}/", $val) )
                {
                    if( $throw_error ) {
                        self::_throw_errmsg( "手机号码不合法" );
                    } else {
                        $val = '';
                    }
                }
                break;
            case 'ip':
                if( !self::_test_ip($val) ) {
                    if( $throw_error ) {
                        self::_throw_errmsg( "IP地址不合法" );
                    } else {
                        $val = '';
                    }
                }
                break;
            case 'var':
                $val = preg_replace("/[^\w]/", '', $val);
                break;
            case 'hash':
                $val = preg_replace("/[^0-9a-zA-Z]/", '', $val);
                break;
            case 'keyword':
                $val = self::_filter_keyword($val);
                $val = util::utf8_substr($val, 30);
                break;
            case 'xss_clean':
                $val = cls_security::xss_clean($val);
                break;
            default:
                if ( function_exists($type)) 
                {
                    $val = $type($val);
                }
                break;
        }
        return $val;
    }

    /**
     * 检测用户名
     * @param string $user_name
     * @return bool
     */
    private static function _test_user_name($user_name)
    {
        return preg_match('/^[a-z0-9\x{4e00}-\x{9fa5}]+[_a-z0-9\x{4e00}-\x{9fa5}\-]+$/iu', $user_name)
            && strlen($user_name) >= 0 && mb_strlen($user_name, 'UTF-8') <= 30;
    }

    /**
     * 替换关键字非法字符（允许空格和个别特殊符号）
     * @param string $keyword
     * @return bool
     */
    private static function _filter_keyword($val)
    {
        return preg_replace('/[^a-z0-9\x{4e00}-\x{9fa5} _#:@\.\t\+\-]/iu', ' ', $val);
    }

    /**
     * 检测字符串是否为email
     */
    private static function _test_email($str)
    {
        return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $str);
    }

    /**
     * 检测字符串是否为ip
     */
    private static function _test_ip($ip)
    {
        return preg_match('/((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}/', $ip);
    }

    /**
     *  抛出异常
     */
    private static function _throw_errmsg($msg)
    {
        throw new Exception( $msg );
    }


}
