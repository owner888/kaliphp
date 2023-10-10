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
use kaliphp\util;
use Exception;

/**
 * 数据过滤类(这个类只对不符合类型的字符进行过滤，数据验证使用cls_validate.php类)
 *
 * @version 1.0
 */
class cls_filter
{ 
    
    // 过滤类型
    protected $_filter_types = array('int',         // 0-12位的数字(可包含-)
                                     'float',       // 小数
                                     'string',      // 字符串
                                     'bool',        // 布尔类型
                                     'array',       // 数组
                                     'object',      // 对象
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
     *
     * @param mixed $val          变量值
     * @param string $type        当type为数字的时候，表示截取指定长度的字符
     * @param bool $throw_error   是否抛出异常(只对邮箱、用户名、qq、手机类型有效)，如果不抛出异常，会对无效的数据设置为空
     *                            (此值用户不直接使用，一般通过 req::$throw_error 进行设置)
     * @return mixed
     */
    public static function filter($val, string $type = '', bool $throw_error = false)
    {
        // 没指定过滤类型，不处理
        if( $type == null )
        {
            return $val;
        }

        // 值为数组类型，递归过滤，需要判断是否为空，因为为空需要进入else，比如空的array，强制转为object，一般是json接口用
        if ( is_array($val) && !empty($val) ) 
        {
            foreach ($val as $k => $v ) 
            {
                $val[$k] = self::filter($v, $type, $throw_error);
            }
        }
        else 
        {
            // type为array，说明是 [$class, $method] 这种处理方式
            if ( is_array($type)) 
            {
                $val = call_user_func($type, $val);
                return $val;
            }

            $type = strtolower($type);
            $val  = is_string($val) ? trim($val) : $val;
            switch( $type )
            {
                case 'int':
                    $val = intval($val);
                    break;
                case 'float':
                    $val = floatval($val);
                    break;
                case 'string':
                    $val = htmlspecialchars(trim((string) $val), ENT_QUOTES);
                    break;
                case 'bool':
                    $val = (bool) $val;
                    break;
                case 'array':
                    $val = (array) $val;
                    break;
                case 'object':
                    $val = (object) $val;
                    break;
                case 'stripslashes':
                    $val = $val && !is_object($val) ? stripslashes($val) : $val;
                    break;
                case 'htmlentities':
                    // 同时转义双,单引号
                    $val = htmlspecialchars(trim($val), ENT_QUOTES);
                    break;
                case 'email':
                    if( !self::_test_email($val) )
                    {
                        if( strlen($val) > 0 && $throw_error ) 
                        {
                            self::_throw_errmsg("Email不合法");
                        } 
                        else 
                        {
                            $val = '';
                        }
                    }
                    break;
                case 'username':
                    if( !self::_test_user_name($val) )
                    {
                        if( $throw_error ) 
                        {
                            self::_throw_errmsg("用户名不合法");
                        } 
                        else 
                        {
                            $val = '';
                        }
                    }
                    break;
                case 'qq':
                    $val = preg_replace("/[^0-9]/", '', $val);
                    if( strlen($val) < 5 )
                    {
                        if( $val > 0 && $throw_error ) 
                        {
                            self::_throw_errmsg("QQ号码不合法");
                        } 
                        else 
                        {
                            $val = '';
                        }
                    }
                    break;
                case 'mobile':
                    $val = preg_replace("/[^0-9]/", '', $val);
                    if( !preg_match("/1[3-9]{10}/", $val) )
                    {
                        if( $throw_error ) 
                        {
                            self::_throw_errmsg("手机号码不合法");
                        } 
                        else 
                        {
                            $val = '';
                        }
                    }
                    break;
                case 'ip':
                    if( !self::_test_ip($val) ) 
                    {
                        if( $throw_error ) 
                        {
                            self::_throw_errmsg("IP地址不合法");
                        } 
                        else 
                        {
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
                    if ( function_exists($type) && $val != null ) 
                    {
                        $val = $type($val);
                    }
                    break;
            }
        }

        return $val;
    }

    /**
     * 数据过滤，用于过滤，设置默认值，执行回掉函数，用于对用户提交的数据进行处理，
     * 配置如果有指定required，如果某个字段没有，则返回字段名称，如果规则通过返回array
     *
     * type             数据类型
     * required         是否必须
     * default          默认值
     * callback         回调函数
     * length           截取长度
     * map_field        映射字段 a => b
     * from_charset     从xx转换编码
     * charset          转换成xx编码
     * _config_         配置是否过滤空值
     *
     * cls_filter::data([
     *     'bill_id'  => ['type' => 'int',   'default' => util::make_bill_id(), 'callback' => 'abs', 'max' => 19],
     *     'amount'   => ['type' => 'float', 'default' => 0.01,                 'callback' => 'abs'], 
     *     '_config_' => ['filter_null' => true]
     * ], $data);
     *
     * @param  array $filter        过滤条件
     * @param  array $data          过滤数据
     * @param  bool  $magic_slashes 去掉魔法引号
     *
     * @return array $ret 过滤后结果
     */
    public static function data(array $filter, array $data, bool $magic_slashes = true)
    {
        // 去掉魔法引号
        if ( $magic_slashes )
        {
            $data = self::filter( $data, 'stripslashes');
        }
        
        // 用于配置过滤空值
        if (!empty($filter['_config_']))
        {
            $ext_config = $filter['_config_'];
            unset($filter['_config_']);
        }

        $ret = array();
        foreach ($filter as $field => $config)
        {
            $default  = null;
            $is_array = false;
            if (is_array($config))
            {
                $is_array = true;
                $required = cls_arr::get($config, 'required', false);
                if ($required)
                {
                    if (!isset($data[$field]))
                    {
                        return $field;
                    }
                }

                // 来源映射
                if( !empty($config['input_field']) )
                {
                    $config['map_field'] = $field;
                    $field = $config['input_field'];
                }

                // 递归
                if (!empty($config['filter']))
                {
                    $ret[$field] = isset($data[$field]) ?
                        self::data($config['filter'], (array)$data[$field], false) : array();
                    continue;
                }

                $type = $config['type'] ?? 'text';

                if (isset($config['default']))
                {
                    $default = $config['default'];
                }
            }
            else
            {
                $type = $config;
                $config = array();
            }

            // 过滤空项
            if (
                // 去掉为null的值
                (
                    !empty($ext_config['filter_null']) &&
                    null === $default && (!isset($data[$field]))
                ) ||
                // 去掉非0空值
                (
                    !empty($ext_config['filter_empty']) &&
                    null === $default &&
                    (!isset($data[$field]) || (isset($data[$field]) && $data[$field] !== 0 && empty($data[$field])))
                ) ||
                // 去掉指定字段空值
                (
                    !empty($ext_config['filter_fields']) && in_array($field, (array)$ext_config['filter_fields']) && empty($data[$field])
                )
            )
            {
                // 存在忽略字段
                if (
                    !isset($ext_config['exclude_fields']) ||
                    (isset($ext_config['exclude_fields']) && !in_array($field, (array)$ext_config['exclude_fields']))
                )
                {
                    continue;
                }
            }

            switch ($type)
            {
            case 'bool_int':
                $ret[$field] = empty($data[$field]) ? 0 : 1;
                break;
            case 'bool':
                $ret[$field] = !empty($data[$field]) ? true : false;
                break;

            case 'int':
                $ret[$field] = isset($data[$field]) ? self::filter( $data[$field], 'int') : $default;
                if ($is_array && isset($config['min']))
                {
                    $ret[$field] = max($config['min'], $ret[$field]);
                }

                if ($is_array && isset($config['max']))
                {
                    $ret[$field] = min($config['max'], $ret[$field]);
                }
                break;

            case 'float':
            case 'double':
                $ret[$field] = isset($data[$field]) ? self::filter( $data[$field], 'float') : $default;
                if ($is_array && isset($config['min']))
                {
                    $ret[$field] = max($config['min'], $ret[$field]);
                }

                if ($is_array && isset($config['max']))
                {
                    $ret[$field] = min($config['max'], $ret[$field]);
                }
                break;

            case 'mixed':
            case 'html':
                $ret[$field] = $data[$field] ?? $default;
                break;

            case 'json':
                $ret[$field] = isset($data[$field]) ? json_encode($data[$field]) : $default;
                $ret[$field] = addslashes($ret[$field]);
                break;

            case 'serialize':
                $ret[$field] = isset($data[$field]) ? serialize($data[$field]) : $default;
                $ret[$field] = addslashes($ret[$field]);
                break;

            case 'regex':
                if (!isset($config['regex']))
                {
                    $ret[$field] = $data[$field] ?? $default;
                    break;
                }

                $replace = $config['replace'] ?? '';
                $ret[$field] = isset($data[$field]) ? preg_replace($config['regex'], $replace, $data[$field]) : $default;
                break;

            case 'callback':
                if (
                    isset($data[$field]) &&
                    isset($config['callback']) && is_callable($config['callback'])
                )
                {
                    $ret[$field] = call_user_func($config['callback'], $data[$field]);
                }
                else
                {
                    $ret[$field] = $default;
                }
                break;
            case 'array':
                if(isset($data[$field]) && !is_array($data[$field]))
                {
                    return $field;
                } 
                
                $ret[$field] = isset($data[$field]) ? (array) $data[$field] : $default;
                break;

            case 'text':
            default:
                $ret[$field] = isset($data[$field]) ? self::filter( $data[$field], 'htmlentities') : $default;
                if ( !is_array($ret[$field]))
                {
                    $ret[$field] = trim($ret[$field]);
                    $charset = $config['charset'] ?? 'utf-8';
                    if (
                        isset($config['from_charset']) &&
                        !mb_check_encoding($ret[$field], $charset) &&
                        $to = mb_detect_encoding($ret[$field], $config['from_charset'])
                    )
                    {
                        $ret[$field] = mb_convert_encoding($ret[$field], $charset, $to);
                    }

                    if ( isset($config['length']))
                    {
                        $ret[$field] = mb_substr(
                            $ret[$field],
                            0, $config['length'],
                            $charset
                        );
                    }
                }

                break;
            }

            //空值直接返回当前字段
            if ( empty($config['required']) && !empty($config['not_empty']) && empty($ret[$field]) ) 
            {
                return $field;
            }

            // 过滤后回调
            if (!empty($ret[$field]) && 
                isset($config['callback']) && is_callable($config['callback']))
            {
                if (is_array($ret[$field]))
                {
                    $ret[$field] = array_map($config['callback'], $ret[$field]);
                }
                else
                {
                    $ret[$field] = call_user_func($config['callback'], $ret[$field]);
                }
            }

            // 添加映射字段
            if( !empty($config['map_field']) )
            {
                $ret[$config['map_field']] = $ret[$field];
                unset($ret[$field]);
            }
        }

        return $ret;
    }

    /**
     * 检测用户名
     *
     * @param string $user_name
     *
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

