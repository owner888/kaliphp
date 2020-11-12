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
use kaliphp\lib\cls_arr;
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
    private $_module = 'config';
    private $_source = 'file';
    private $_cfg_caches = [];
    private $_alias = [];

    /**
     * 单例
     *
     * @param mixed $name config|app_config|db_config
     * config 在 kali/config
     * app_config 在 app/config
     * db_config 在 数据库里面
     * @return config
     */
    public static function instance($module = 'config', $source = 'file')
    {
        if (!isset(self::$_instance[$source][$module]))
        {
            self::$_instance[$source][$module] = new self($module, $source);
        }

        return self::$_instance[$source][$module];
    }

    /**
     * config constructor.
     *
     * @param $name
     */
    private function __construct($module = 'config', $source = 'file')
    {
        $this->_module = $module;
        $this->_source = $source;
    }

    /**
     * 加载系统配置文件
     * 先加载对应的配置，比如database.php，看看有没有相应环境的配置，比如database_dev.php，有就覆盖
     *
     * @throws Exception
     */
    public function load()
    {
        if ( !isset($this->_cfg_caches[$this->_source][$this->_module]) ) 
        {
            $this->_cfg_caches[$this->_source][$this->_module] = [];

            if ( $this->_source === 'db' ) 
            {
                $this->_cfg_caches[$this->_source][$this->_module] = $this->cache();
            }
            else 
            {
                $env = $this->_module. (ENV_DEV ? '_dev' : (ENV_PRE ? '_pre' : (ENV_PUB ? '_pub' : '')));

                $config_paths[] = __DIR__ . DS . 'config' . DS;
                if ( defined('APPPATH')) 
                {
                    $config_paths[] = APPPATH. DS . 'config' . DS;
                }

                $config_path = '';
                //如果有config$env优先使用，否则加载哪里config
                //config优先顺序 数据库 -> app config -> 系统config
                foreach($config_paths as $path)
                {
                    $config_path = $path.$this->_module.'.php';

                    if( file_exists($file = $path.$env.'.php') || file_exists($file = $path.$this->_module.'.php') )
                    {
                        $config = require $file; 
                        $this->_cfg_caches[$this->_source][$this->_module] = util::array_merge_multiple(
                            (array) $this->_cfg_caches[$this->_source][$this->_module], 
                            (array) $config
                        );
                    }
                }

                if( empty($this->_cfg_caches[$this->_source][$this->_module]) )
                {
                    throw new Exception($config_path, 1002);
                }
            }
        }

        return $this->_cfg_caches[$this->_source][$this->_module];
    }

    /**
     * 获取/设置配置缓存
     * @param  bool    $update  是否更新
     * @return array   $configs 配置信息
     */
    public function cache(bool $update = false)
    {
        $cache_key = __CLASS__ .':sys_db_config';
        $configs = cache::get($cache_key);
        if( $update || empty($configs) )
        {
            $rsid = db::select('name,value,group')
                ->from('#PB#_config')
                ->as_result()
                ->execute();

            $configs = [];
            while( $row = db::fetch($rsid) )
            {
                $configs[$row['name']] = $row['value'];
            }

            cache::set($cache_key, $configs, 0);
        }

        return $configs;
    }

    /**
	 * Sets a (dot notated) config item
	 *
	 * @param    string   $item   a (dot notated) config key
	 * @param    mixed    $value  the config value
	 */
    public function set($key, $value)
    {
		strpos($key, '.') === false or $this->_cfg_caches[$this->_source][$this->_module][$key] = $value;
		cls_arr::set($this->_cfg_caches[$this->_source][$this->_module], $key, $value);
    }

    /**
     * Returns a (dot notated) config setting
     *
     * @param   string   $item      name of the config item, can be dot notated
     * @param   mixed    $default   the return value if the item isn't found
     * @return  mixed               the config setting or default if not found
     */
    public function get($key = null, $default = null, $alias = true)
    {
        $configs = $this->load();

        $value = (func_num_args() === 0) ? $configs : cls_arr::get($configs, $key, $default);
        $value = $alias ? $this->get_alias($value) : $value;
        $value = empty($value) ? $default : $value;

        return $value;
    }

    /**
     * Deletes a (dot notated) config item
     *
     * @param    string       $item  a (dot notated) config key
     * @return   array|bool          the \Arr::delete result, success boolean or array of success booleans
     */
    public function del($key)
    {
        if ( isset($this->_cfg_caches[$this->_source][$this->_module][$key]) )
        {
            unset($this->_cfg_caches[$this->_source][$this->_module][$key]);
        }

        return cls_arr::delete($this->_cfg_caches[$this->_source][$this->_module], $key);
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

