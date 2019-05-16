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

/**
 * 配置文件类
 */
class config
{
    private static $_instance = [];
    private $_name;
    private $_cfg_caches = [];
    private $_appcfg_caches = [];
    private $_dbcfg_caches = [];
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
     * @param string $module
     * @throws \Exception
     */
    private function load_config($module)
    {
        if (!isset($this->cfg_caches[$module])) 
        {
            $path = kali::$base_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = kali::$base_root . DS . 'config' . DS . $n_module . '.php';
            if ( is_readable($path) || is_readable($n_path) ) 
            {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                $this->_cfg_caches[$module] = $config;
            } 
            else 
            {
                throw new \Exception($path, 1002);
            }
        }

        return $this->_cfg_caches[$module];
    }

    /**
     * 加载应用配置文件
     * 先加载对应的配置，比如database.php，看看有没有相应环境的配置，比如database_dev.php，有就覆盖
     * @param string $module
     * @return mixed
     * @throws TXException
     */
    private function load_app_config($module)
    {
        if (!isset($this->_appcfg_caches[$module])) 
        {
            $path = kali::$app_root . DS . 'config' . DS . $module . '.php';

            $n_module = $module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));
            $n_path = kali::$app_root . DS . 'config' . DS . $n_module . '.php';

            if ( is_readable($path) || is_readable($n_path) ) 
            {
                $config = is_readable($path) ? require($path) : [];
                $config = is_readable($n_path) ? array_merge($config, require($n_path)) : $config;
                $this->_appcfg_caches[$module] = $config;
            } 
            else 
            {
                throw new \Exception($path, 1002);
            }
        }

        return $this->_appcfg_caches[$module];
    }

    /**
     * 加载应用配置文件
     * 先加载对应的配置，比如database.php，看看有没有相应环境的配置，比如database_dev.php，有就覆盖
     * @param string $module
     * @return mixed
     * @throws TXException
     */
    private function load_db_config($module)
    {
        if (!isset($this->_dbcfg_caches[$module])) 
        {
            $this->_dbcfg_caches[$module] = $this->cache($module);
        }

        return $this->_dbcfg_caches[$module];
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
                ['kali\core\cache', 'set'],
                [$cache_key, $configs, 0]
            );
        }

        if( !empty($module) )
        {
            $configs = isset($configs[$module]) ? $configs[$module] : [];
        }

        return $configs;
    }

    /**
     * get core config
     * @param $key
     * @param string $module
     * @param bool $alias
     * @return mixed|null
     */
    public function get($key, $module='config', $alias=true)
    {
        $method = 'load_'.$this->_name;
        //$config = $this->_name === "config" ? $this->load_config($module) : $this->load_app_config($module);
        $config = $this->$method($module);

        if (isset($config[$key])) 
        {
            return $alias ? $this->get_alias($config[$key]) : $config[$key];
        } 
        else 
        {
            return null;
        }
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

