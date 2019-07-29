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
use kaliphp\kali;
use kaliphp\cache;
use kaliphp\db;
use kaliphp\util;
use Exception;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('SYS_ENV') or define('SYS_ENV', 'pub');
defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');

/**
 * 配置文件类
 */
class config
{
    private static $_instance = [];
    private $_name;
    private $_cfg_caches = [];
    private $_alias = [];

    /**
     * 单例
     * @param mixed $name config|app_config|db_config
     * config 在 kali/config
     * app_config 在 app/config
     * db_config 在 数据库里面
     * @return config
     */
    public static function instance($name = 'config')
    {
        if (!isset(self::$_instance[$name]))
        {
            self::$_instance[$name] = new self($name);
        }
        return self::$_instance[$name];
    }

    /**
     * 构造
     * config constructor.
     * @param $name
     */
    private function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * 加载系统配置文件
     * @throws Exception
     */
    private function load_config()
    {
        if (!isset($this->_cfg_caches[$this->_name])) 
        {
            $env_name = $this->_name. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $this->_cfg_caches[$this->_name] = [];

            //如果有config$env_name优先使用，否则加载哪里config
            //config优先顺序 数据库->系统config->app config
            foreach([__DIR__ . DS . 'config' . DS, APPPATH. DS . 'config' . DS] as $path)
            {
                if( file_exists($file = $path.$env_name.'.php') || file_exists($file = $path.$this->_name.'.php') )
                {
                    $config = require $file; 
                    $this->_cfg_caches[$this->_name] = util::array_merge_multiple(
                        (array) $this->_cfg_caches[$this->_name], 
                        (array) $config
                    );
                }
            }

            if( empty($this->_cfg_caches[$this->_name]) )
            {
                throw new Exception($path, 1002);
            }
        }

        return $this->_cfg_caches[$this->_name];
    }

    /**
     * 获取/设置配置缓存
     * @param  string  $module  模块
     * @param  boolean $update  是否更新
     * @return array   $configs 配置信息
     */
    public function cache($module = null, $update = false)
    {
        $cache_key = __CLASS__ .':sys_db_config';
        $configs = cache::get($cache_key);
        if( empty($configs) || !empty($update) )
        {
            $query = db::select('name,value,group')
                ->from('#PB#_config')
                ->as_result()
                ->execute();

            $configs = [];
            while( $row = db::fetch($query) )
            {
                $configs[$row['group']][$row['name']] = $row['value'];
            }

            util::shutdown_function(
                ['kaliphp\cache', 'set'],
                [$cache_key, $configs, 0]
            );
        }

        if( !empty($module) )
        {
            $configs = isset($configs[$module]) ? $configs[$module] : [];
        }

        return $configs;
    }

    public function set( $key, $value )
    {
        if ( empty($key) || empty($value) ) 
        {
            return false;
        }

        $this->_cfg_caches[$this->_name][$key] = $value;
    }

    /**
     * get core config
     * @param $key
     * @param bool $alias
     * @return mixed|null
     */
    public function get( $key = null, $defaultvalue = null, $alias = true )
    {
        $config = $this->load_config();

        if ( $config && $key === null ) 
        {
            return $config;
        }

        if( !isset($config[$key]) || $config[$key] === '' )
        {
            $value = $defaultvalue;
        } 
        else 
        {
            $value = $config[$key];
            $value = $alias ? $this->get_alias($config[$key]) : $config[$key];
        }
        return $value;
    }

    /**
     * 设置别名
     * @param $key
     * @param $value
     */
    public function set_alias($key, $value)
    {
        $this->_alias["@{$key}@"] = $value;
    }

    /**
     * 获取别名转义
     * @param $value
     * @return mixed
     */
    private function get_alias($value)
    {
        if ($this->_alias && is_string($value))
        {
            $value = str_replace(array_keys($this->_alias), array_values($this->_alias), $value);
            return $value;
        } 
        else 
        {
            return $value;
        }
    }

}

/* vim: set expandtab: */

