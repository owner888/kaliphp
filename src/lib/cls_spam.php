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
use kaliphp\req;
use kaliphp\kali;
use kaliphp\config;
use kaliphp\lib\cls_security;

/**
 * @author han 
 * 系统spam相关操作，目的是为了能在后台可视化管理spam数据，否则杂乱无章
 * 使用方法
 *  $key = 'login:username:fuck'; //模块:验证类别:类别值
 *  if( false == cls_spam::check($key, $spam_username) ) 
 *  {
 *      //已经超出阀值
 *  }
 *  //开始验证逻辑
 *  else
 *  {
 *      //如果错误
 *      cls_spam::save($key, $data); //如果不需要保存数据不需要data,total会自增
 *
 *      //如果正确，执行删除spam
 *      cls_spam::clear($key)
 *  }
 *
 * 
 */
class cls_spam 
{
    public static $spam_keys = [],
        $auto_spam = [], //自动执行的spam
        $spam_data = [];

    public static function _init()
    {
        //后面的限制要放到后台中配置
        //limit 系统阀值
        //interval 触发频率
        //后面的限制要放到后台中配置
        // 'spam_config' => [
        //     'login' => [
        //         'label' => '登陆',
        //         'keys'  => [
        //             'email'  => ['label' => '乐马号/邮箱', 'limit' => 3, 'interval' => 1], //默认每天
        //             'phone'  => ['label' => '电话', 'limit' => 3, 'interval' => 1], //默认每天
        //             'potato' => ['label' => 'Potato', 'limit' => 3, 'interval' => 1], //默认每天
        //             'ip'     => ['label' => 'IP', 'limit' => 10], //默认每天
        //         ]
        //     ],
        // ],
        
        $spam_config = config::instance()->get('spam_config');
        self::$spam_keys = array_merge(self::$spam_keys, (array) $spam_config);
    }

    /**
     * 获取spam数据
     * @Author han
     * @param  string $key 比如 login:username:fuck
     * $key = 'login:username:fuck';
     * $data = cls_spam::get($key);
     * [
     *      [total] => 4 //总调用次数，每次保存会+1
     *      [timestamp] => 1551798625 //最后保存时间
     *      [data] => [ //保存的数据
     *          [0] => 1111
     *          [1] => 2222
     *      ]
     *  ]
     * @return array 返回spam数据
     */
    public static function get($key)
    {
        $_key  = self::get_key($key);
        if( !isset(self::$spam_data[$_key]) )
        {
            self::$spam_data[$_key] = cls_security::spam(['key' => $_key]);
            if( !empty(self::$spam_data[$_key]) )
            {
                self::$spam_data[$_key]['limit']    = self::get_system_limit($key);
                self::$spam_data[$_key]['interval'] = self::get_system_limit($key, 'interval');
            }
        }

        return self::$spam_data[$_key];
    }

    /**
     * 检查是否超出spam阀值
     * $key = 'login:username:fuck';
     * $data = cls_spam::check($key);
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @param array 返回spam数据
     * @param int    $limit 0则调用系统默认阀值 
     * @param bool   $auto_ip_spam 如果为true而且配置的IP,则会自动检查ip的限制
     * @return bool   是否超出spam阀值 true 表示正常 false表示已经触发. null频率太快 0触发IP限制 false 当前类型限制
     */
    public static function check($key, &$spam = [], $limit = 0, $auto_ip_spam = true)
    {
        $_key  = self::get_key($key);
        self::$spam_data[$_key] = $spam = self::get($key);

        $limit  = intval($limit);
        //系统设置了使用系统的
        $limit = empty($limit) ? (isset(self::$spam_data[$_key]['limit']) ? 
            self::$spam_data[$_key]['limit'] : 0) : $limit;
        $interval = isset(self::$spam_data[$_key]['interval']) ? 
            self::$spam_data[$_key]['interval'] : 0;

        //检查是否触发频率阀值
        if( 
            !empty($interval) &&
            !empty(self::$spam_data[$_key]['timestamp']) &&
            (KALI_TIMESTAMP - self::$spam_data[$_key]['timestamp'] <= $interval) )
        {
            $ret = null; //区别于触发阀值
        }
        else
        {
            // 0表示不限制
            $ret = self::$spam_data[$_key]['total'] < $limit || $limit === 0;

            //如果limit未ip,则会自动检查ip的限制
            if( !empty($ret) && !empty($auto_ip_spam) )
            {
                if( false != ($ip_key = self::get_ip_key($key)) )
                {  
                    $ret = self::check($ip_key, $ip_spam, 0, false);
                    $ret = $ret == false ? 0 : $ret;
                    self::$auto_spam[$ip_key] = $ip_spam;
                }
            }
        }

        true != $ret && log::error($key, "cls_spam:check_error:{$ret}");
        return $ret;
    }

    /**
     * 设置spam数据
     * $key = 'login:username:fuck';
     * $data = cls_spam::save($key, ['fuck', 'you']);
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @return bool 返回操作结果
     */
    public static function save($key, array $data = [])
    {
        $_key = self::get_key($key);

        //更新内存变量
        self::$spam_data[$_key]['total'] = empty(self::$spam_data[$_key]['total']) ? 
            0 : self::$spam_data[$_key]['total'];
        self::$spam_data[$_key]['total']++;
        self::$spam_data[$_key]['data'] = $data;
        self::$spam_data[$_key]['timestamp'] = KALI_TIMESTAMP;

        $ret = cls_security::spam([
            'key'    => $_key, 
            'data'   => $data, 
            'action' => 'save'
        ]);

        //按模块方法统计触发次数和剩余次数
        $ma = self::get_module_action($key);
        list($m, $a, ) = array_values($ma);
        if( isset(self::$spam_keys[$m]['keys'][$a]['limit']) )
        {
            self::$spam_data[$m][$a] = [
                'limit'   => self::$spam_keys[$m]['keys'][$a]['limit'],
                'total'   => self::$spam_data[$_key]['total'],
                'surplus' => self::$spam_keys[$m]['keys'][$a]['limit'] - self::$spam_data[$_key]['total'],
            ];
        }

        //是否存在自动执行的spam
        if( !empty($ret) && !empty(self::$auto_spam) )
        {
            list($module, $action, $val) = explode(':', $key);
            foreach(self::$auto_spam as $ip_key => $spam)
            {
                self::$auto_spam[$ip_key]['data'] = isset(self::$auto_spam[$ip_key]['data']) ? 
                    (array) self::$auto_spam[$ip_key]['data'] : [];

                $ret = cls_security::spam([
                    'key'    => self::get_key($ip_key), 
                    'data'   => array_merge(self::$auto_spam[$ip_key]['data'], [$val]), 
                    'action' => 'save'
                ]);

                //执行完毕后注销
                unset(self::$auto_spam[$ip_key]);
            }
        }

        return $ret;
    }

    /**
     * 追加spam数据
     * $key = 'login:username:fuck';
     * $data = cls_spam::add($key, ['fuck', 'you']);
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @return bool 返回操作结果
     */
    public static function add($key, array $data)
    {
        $_key = self::get_key($key);
        self::$spam_data[$_key] = self::get($key);

        self::$spam_data[$_key]['data'] = isset(self::$spam_data[$_key]['data']) ? 
            (array) self::$spam_data[$_key]['data'] : [];

        return self::save(
            $key, 
            array_merge(self::$spam_data[$_key]['data'], $data)
        );
    }

    /**
     * 删除spam数据
     * $key = 'login:username:fuck';
     * $data = cls_spam::clear($key);
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @param bool $auto_clear 是否自动清理
     * @return bool 返回操作结果
     */
    public static function clear($key, $auto_clear = true)
    {
        $_key = self::get_key($key);
        self::$spam_data[$_key] = [];

        $ret = cls_security::spam(['key' => $_key, 'action' => 'clear']);
        //如果limit未ip,则会自动检查ip的限制
        if( 
            !empty($ret) && !empty($auto_clear) && 
            false != ($ip_key = self::get_ip_key($key))
        )
        {
            $ret = cls_security::spam([
                'key'    => self::get_key($ip_key), 
                'action' => 'clear'
            ]);
        }

        return $ret;
    }

    /**
     * 获取系统阀值
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @param  string $type 阀值类型
     * @param  int    $limit 统默认阀值（不在系统配置的返回0）
     */
    public static function get_system_limit($key, $type = 'limit')
    {
        $limit = 0; //非系统配置的默认为0，需要程序自己写限制数量
        $tmp = self::get_module_action($key);
        if( !empty($tmp) )
        {
            list($m, $k, $v) = array_values($tmp);
            if( isset(self::$spam_keys[$m]['keys'][$k][$type]) )
            {
                $limit = self::$spam_keys[$m]['keys'][$k][$type];
            }
        }
        
        return $limit;
    }


    /**
     * 获取键名称
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @return string      返回系统键值
     */
    public static function get_key($key)
    {
        static $keys;
        if( !isset($keys[$key]) )
        {
            $tmp = self::get_module_action($key);
            //如果键不符合规格，则后台不能管理，需要自己管理
            if( empty($tmp) )
            {
                list($m, $k, $v) = $tmp;
                $_key = $m .':' .$k.':'.$v;
            }
            else
            {
                $_key = $key;
            }

            $_key .= '_'.date('Y-m-d'); 
            $keys[$key] = $_key;
        }

        return $keys[$key];
    }

    /**
     * 根据其他键获取相应的ip键
     * @Author han
     * @param  string $key 如果不在spam_keys的键将不会在后台控制
     * @return string
     */
    public static function get_ip_key($key)
    {
        $tmp = self::get_module_action($key);
        $ip_key = '';

        if( !empty($tmp) )
        {
            list($module, $action, $val) = array_values($tmp);
            $keys = isset(self::$spam_keys[$module]['keys']) ? 
                self::$spam_keys[$module]['keys'] : [];

            //删除当前action
            unset($keys[$action]);
            if( isset($keys['ip']) )
            {
                $ip_key = "{$module}:ip:".self::get_ip();
            }
        }

        return $ip_key;
    }

    public static function get_module_action($key) 
    {
        static $module_actions = [];
        if( !isset($module_actions[$key]) )
        {
            $tmp = explode(':', $key);
            if( count($tmp) == 3 )
            {
                list($module, $action, $val) = $tmp;
                $module_actions[$key] = ['m' => $module, 'a' => $action, 'v' => $val];
            }
        }

        return $module_actions[$key];
    }

    private static function get_ip()
    {
        defined('IP') or define('IP', req::ip());
        return IP == '0.0.0.0' ? '127.0.0.1' : IP;
    }

    /**
     * 获取系统键
     * @Author han
     * @param  string $module 模块
     * @return array  模块下的系统键
     */
    public static function list_keys($module = '')
    {
        $ret = [];
        if( empty($module) )
        {
            foreach(self::$spam_keys as $key => $conf)
            {
                $ret[$key] = $conf['label'];
            }
        }
        else if( isset(self::$spam_keys[$module]) )
        {
            foreach(self::$spam_keys[$module]['keys'] as $key => $conf)
            {
                $ret["{$module}:{$key}"] = $conf['label'];
            }
        }

        return $ret;
    }
}
