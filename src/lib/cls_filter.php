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
    protected $_filter_types = [
        'int',         // 0-12位的数字(可包含-)
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
    ];

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
        if ( $type == null )
        {
            return $val;
        }

        // 值为数组类型，递归过滤，需要判断是否为空，因为为空需要进入else，比如空的array，强制转为object，一般是json接口用
        if ( is_array($val) && !in_array(strtolower($type), ['object', 'array']) ) 
        {
            foreach ($val as $k => $v ) 
            {
                $val[$k] = self::filter($v, $type, $throw_error);
            }
        }
        else 
        {
            // type为array，说明是 [$class, $method] 这种处理方式
            if ( is_array($type) ) 
            {
                $val = call_user_func($type, $val);
                return $val;
            }

            $type = strtolower($type);
            if ( !isset($val) ) 
            {
                return null;
            }

            $val = is_string($val) ? trim($val) : $val;
            switch( $type )
            {
                case 'int':
                    $val = intval($val);
                    break;
                case 'gt0': // 参数大于0，小于则返回0
                    $val = isset($val) ? max(0, intval($val)) : null;
                    break;
                case 'float':
                    $val = floatval($val);
                    break;
                case 'string':
                    $val = $val ? self::filter(strip_tags(htmlspecialchars_decode($val)), 'htmlentities') : $val;
                    break;
                //安全可信的string，彻底杜绝sql注入,对于那种自己要使用英文的()，可以用string类型，但是一定要考虑sql注入问题
                case 'text':
                case 'safe_str': 
                    $val = str_replace(
                        ['(', ')'], ['（', '）'], 
                        self::filter($val, 'string') ?? ''
                    );

                    break;
                case 'bool':
                    $val = (bool) $val;
                    break;
                case 'array':
                    $val = (array) self::filter($val, 'safe_str');
                    break;
                case 'object':
                    $val = (object) $val;
                    break;
                case 'stripslashes':
                    $val = $val && is_string($val) ? stripslashes($val) : $val;
                    break;
                case 'html':
                case 'htmlentities':
                    // 同时转义双,单引号
                    $val = $val ? htmlspecialchars(trim($val), ENT_QUOTES) : $val;
                    break;
                case 'email':
                    if ( !self::test_email($val) )
                    {
                        if ( strlen($val) > 0 && $throw_error ) 
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
                    if ( !self::_test_user_name($val) )
                    {
                        if ( $throw_error ) 
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
                    if ( strlen($val) < 5 )
                    {
                        if ( $val > 0 && $throw_error ) 
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
                    if ( !preg_match("/1[3-9]{10}/", $val) )
                    {
                        if ( $throw_error ) 
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
                    if ( !self::test_ip($val) ) 
                    {
                        if ( $throw_error ) 
                        {
                            self::_throw_errmsg("IP地址不合法");
                        } 
                        else 
                        {
                            $val = '';
                        }
                    }
                    break;
                case 'id_card':
                    if ( !self::test_is_idcard($val) ) 
                    {
                        if ( $throw_error ) 
                        {
                            self::_throw_errmsg("身份证不合法");
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
                    if ( function_exists($type)) 
                    {
                        $val = $type($val ?? '');
                    }
                    else
                    {
                        $val = self::filter($val, 'safe_str');
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
     * input_field      来源字段
     * as_field         映射字段 a => b
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
     * @param  bool  $magic_slashes 去掉魔法引号,框架已经默认转义过一次，所以这里再去掉一下
     * @return mixed $ret 过滤后结果
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
            if ( is_array($config) )
            {
                // 来源映射
                if ( !empty($config['input_field']) )
                {
                    $config['as_field'] = $field;
                    $field = $config['input_field'];
                }

                $is_array = true;
                $required = cls_arr::get($config, 'required', false);
                if ( $required )
                {
                    if (!isset($data[$field]))
                    {
                        return $field;
                    }
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
                empty($config['not_empty']) &&
                // 去掉为null的值
                (
                    !empty($ext_config['filter_null']) &&
                    null === $default && (!isset($data[$field]))
                ) ||
                // 去掉非0空值
                (
                    !empty($ext_config['filter_empty']) &&
                    null === $default &&
                    (
                        !isset($data[$field]) || 
                        (
                            isset($data[$field]) && 
                            $data[$field] !== 0 && empty($data[$field])
                        )
                    )
                ) ||
                // 去掉指定字段空值
                (
                    !empty($ext_config['filter_fields']) && 
                    in_array($field, (array)$ext_config['filter_fields']) && 
                    empty($data[$field])
                )
            )
            {
                // 存在忽略字段
                if (
                    !isset($ext_config['exclude_fields']) ||
                    (
                        isset($ext_config['exclude_fields']) && 
                        !in_array($field, (array)$ext_config['exclude_fields'])
                    )
                )
                {
                    continue;
                }
            }

            // 过滤指定值
            if ( isset($config['filter_value']) ) 
            {
                $data[$field] = cls_arr::filter_value($data[$field], $config['filter_value']);
            }

            switch ($type)
            {
                case 'bool_int':
                    $ret[$field] = empty($data[$field]) ? 0 : 1;
                    break;
                case 'bool':
                    $ret[$field] = (bool) ($data[$field] ?? ($default ?? false));
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
                    $ret[$field]  = $data[$field] ?? $default;
                    break;
                case 'html':
                    $data[$field] = $data[$field] ?? $default;
                    $ret[$field]  = self::filter($data[$field], 'htmlentities');
                    break;

                case 'json':
                    $ret[$field] = isset($data[$field]) ? json_encode($data[$field]) : $default;
                    break;

                case 'serialize':
                    $ret[$field] = isset($data[$field]) ? serialize($data[$field]) : $default;
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
                case 'array': //如果数组中的元素需要类型过来加一个sub_type，默认safe_str
                    $data[$field] = $data[$field] ?? (array) $default;
                    if (isset($data[$field]) && !is_array($data[$field]))
                    {
                        return $field;
                    }

                    $ret[$field] = !empty($config['sub_type']) ? self::filter($data[$field], $config['sub_type']) : $data[$field];
                    break;
                case 'text':
                case 'safe_str':
                    $ret[$field] = isset($data[$field]) ? self::filter( $data[$field], 'safe_str') : $default;
                    break;
                case 'string':
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

            //必须在数据组检查
            if ( 
                !empty($config['in_array']) && 
                is_array($config['in_array']) && 
                !in_array($ret[$field], $config['in_array']) 
            ) 
            {
                return $field;
            }

            // 过滤后回调
            if ( isset($config['callback']) ) 
            {
                //单个函数或者闭包
                if ( is_callable($config['callback']) ) 
                {
                    $funcs = [$config['callback']];
                }
                //支持多个函数
                else
                {
                    $funcs = explode('|', $config['callback']);
                }

                //当前字段不需要指定，如果要使用当前做参数的时候指定一个不存在的field,一般用row
                $callback_field = $config['callback_field'] ?? $field;
                $callback_param = $ret[$callback_field] ?? ($config['default'] ?? null);
                foreach ($funcs as $func)
                {
                    if ( is_callable($func) )
                    {
                        if (is_array($ret[$field]))
                        {
                            $ret[$field] = array_map(function($v) use($func, $data) {
                                if ($func instanceof \Closure) 
                                {
                                    return call_user_func_array($func, [$v, $data]);
                                }
                                else 
                                {
                                    return call_user_func_array($func, [$v]);
                                }
                                
                            }, $callback_param);
                        }
                        else
                        {
                            if ($func instanceof \Closure) 
                            {
                                $ret[$field] = call_user_func_array($func, [$callback_param, $data]);
                            }
                            else 
                            {
                                $ret[$field] = call_user_func_array($func, [$callback_param]);
                            }
                        }
                    }
                }
            }

            // 添加映射字段
            if ( !empty($config['as_field']) )
            {
                $ret[$config['as_field']] = $ret[$field];
                unset($ret[$field]);
            }

            // 比较运算
            $operate_maps = [
                'egt' => '>=',
                'gt'  => '>',
                'elt' => '<=',
                'lt'  => '<'
            ];
            foreach ($operate_maps as $f => $fv) 
            {
                if ( isset($config[$f]) && !util::operation($ret[$field], $fv, $config[$f])) 
                {
                    return $field;
                }
            }
        }

        // 如果去掉了转移，需要加一次
        // $magic_slashes && $ret = req::add_s($ret);        
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
    public static function test_email($str)
    {
        return preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $str);
    }

    /**
     * 检测字符串是否为ip
     */
    public static function test_ip($ip)
    {
        return preg_match('/((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}/', $ip);
    }

    /**
     * 是否为身份证
     * @param    string    $id_card
     * @return   bool       
     */
    public static function test_is_idcard($id_card)
    {
        $cities = [
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        ];

        if ( 
            !preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $id_card) || 
            !in_array(substr($id_card, 0, 2), $cities)
        ) return false;

        $id_card = preg_replace('/[xX]$/i', 'a', $id_card);
        $length = strlen($id_card);
        if ( $length == 18 ) 
        {
            $birth_day = substr($id_card, 6, 4) . '-' . substr($id_card, 10, 2) . '-' . substr($id_card, 12, 2);
        } 
        else 
        {
            $birth_day = '19' . substr($id_card, 6, 2) . '-' . substr($id_card, 8, 2) . '-' . substr($id_card, 10, 2);
        }

        if (date('Y-m-d', strtotime($birth_day)) != $birth_day) return false;
        if ($length == 18) 
        {
            $v_sum = 0;
            for ($i = 17 ; $i >= 0 ; $i--) 
            {
                $vSubStr = substr($id_card, 17 - $i, 1);
                $v_sum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }

            if ($v_sum % 11 != 1) return false;
        }

        return true;
    }

    /**
     *  抛出异常
     */
    private static function _throw_errmsg($msg)
    {
        throw new Exception( $msg );
    }


}

