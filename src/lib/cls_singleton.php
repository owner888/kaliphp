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

abstract class cls_singleton
{
    /**
     * Array of cached singleton objects
     *
     * @var array
     */
    private static $instances = [];

    /**
     * Static method for instantiating a singleton object.
     *
     * @return object
     */
    final public static function instance()
    {
        $class_name = get_called_class();

        return self::$instances[$class_name] ?? new $class_name;
    }

    /**
	 * Singleton objects should not be cloned
     *
	 * @return void
	 */
	final private function __clone() {}

    /**
	 * Similar to a get_called_class() for a child class to invoke.
     *
     * @return string
     */
    final protected function get_called_class()
    {
        $backtrace = debug_backtrace();
        return get_class($backtrace[2]['object']);
    }
}
