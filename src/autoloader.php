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

namespace kali\core;

/**
 * autoloader.
 */
class autoloader
{
    /**
     * Autoload root path.
     *
     * @var string
     */
    protected static $_autoload_root_path = '';

    /**
     * Set autoload root path.
     *
     * @param string $root_path
     * @return void
     */
    public static function set_root_path($root_path)
    {
        self::$_autoload_root_path = $root_path;
    }

    public static function load_by_namespace($class)
    {
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        if (self::$_autoload_root_path) 
        {
            $class_file = self::$_autoload_root_path . DIRECTORY_SEPARATOR . $class_path . '.php';
        }
        if (empty($class_file) || !is_file($class_file)) 
        {
            $class_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "$class_path.php";
        }

		// include the file if needed
        if (is_file($class_file)) 
        {
            require_once($class_file);
        }

        // if the loaded file contains a class...
        if (class_exists($class, false))
        {
            if (method_exists($class, '_init') and is_callable($class.'::_init'))
            {
                call_user_func($class.'::_init');
            }
            return true;
        }

        return false;
    }

}

spl_autoload_register('kali\core\autoloader::load_by_namespace', true, true);

/* vim: set expandtab: */

