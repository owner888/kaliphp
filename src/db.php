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

namespace kaliphp;

use kaliphp\database\db_connection;
use kaliphp\database\db_expression;

/**
 * 数据库类
 *
 * @version 2.0
 */
class db
{
    public static $config = [];
    public static $queries = [];
    public static $query_times = [];
    public static $query_db_names = [];

    // Query types
    const SELECT =  1;
    const INSERT =  2;
    const UPDATE =  3;
    const DELETE =  4;

    /**
     * 初始化
     */
    public static function _init()
    {
    }

    /**
     * 单例
     * @param string $name
     * @param bool $instance
     * @return db
     */
    public static function instance($name = 'default_w', $config = [])
    {
        return db_connection::instance($name, $config);
    }

    /**
     * 初始化数据库
     * @param string $name 实例名称
     * @param string $name 数据库配置文件名
     * @param boll $$default_instance 是否设置为默认数据库
     */
    public static function init_db($name = null, $database = null, $default_instance = false)
    {
        return db_connection::init_db($name, $database, $default_instance);
    }

    /**
     * 切换数据库
     * @param string $name 实例名称
     */
    public static function switch_db($name = null)
    {
        return db_connection::switch_db($name);
    }

    /**
     * SQL查询.
     *
     *     // SELECT
     *     $query = db::query('SELECT * FROM users');
     *
     *     // DELETE
     *     $query = db::query('DELETE FROM users WHERE id = 5');
     * 
     * @param string $sql
     * @param integer $type      db::SELECT, db::INSERT, etc
     * @return object   SELECT 查询结果
     * @return array    list (insert id, row count) for INSERT queries
     * @return integer  number of affected rows for all other queries
     */
    public static function query($sql, array $params = [])
    {
        return db_connection::instance()->query($sql, $params);
    }


    /*
     * Returns the last query
     *
     * @return  string  the last query
     */
    public static function last_query()
    {
        return end(self::$queries);
    }

    public static function enable_slave($enable_slave = true)
    {
        return db_connection::instance()->enable_slave($enable_slave);
    }

    /**
     * select
     * 
     * @param string $select
     * @param mixed $db  select默认会读取从库，提供db类方便读取主库
     * @return db_connection
     */
    public static function select($select = '*') 
    {
        return db_connection::instance()->select($select);
    }

    /**
     * select_count
     * 
     * @param string $select
     * @param mixed $db  select默认会读取从库，提供db类方便读取主库
     * @return db_connection
     */
    public static function select_count($table, $where = []): int
    {
        return (int) db_connection::instance()
            ->select('COUNT(*) AS `count`')
            ->from($table)
            ->where($where)
            ->as_field()
            ->execute();
    }

    /**
     * insert
     *
     * @param null $table
     * @param array|null $columns
     * @return db_connection
     */
    public static function insert($table = null, array $columns = [])
    {
        return db_connection::instance()->insert($table, $columns);
    }

    /**
     * update
     *
     * @param null $table
     * @return db_connection
     */
    public static function update($table = null)
    {
        return db_connection::instance()->update($table);
    }

    /**
     * @param null $table
     * @return db_connection
     */
    public static function delete($table = null)
    {
        return db_connection::instance()->delete($table);
    }

    public static function has_where() 
    {
        return db_connection::instance()->has_where();
    }

    public static function fetch($rsid = null, $result_type = MYSQLI_ASSOC)
    {
        return mysqli_fetch_array($rsid, $result_type);
    }

    public static function autocommit($mode = false)
    {
        return db_connection::instance()->autocommit($mode);
    }

    public static function start($instance = null)
    {
        return db_connection::instance($instance)->start();
    }

    public static function commit($instance = null)
    {
        return db_connection::instance($instance)->commit();
    }

    public static function rollback($instance = null)
    {
        return db_connection::instance($instance)->rollback();
    }

    public static function end($instance = null)
    {
        return db_connection::instance($instance)->end();
    }

    /**
     * Quote a value for an SQL query.
     *
     * @param   string  $string the string to quote
     * @param   string  $db     the database connection to use
     * @return  string  the quoted value
     */
    public static function quote($string)
    {
        if (is_array($string))
        {
            foreach ($string as $k => $s)
            {
                $string[$k] = static::quote($s);
            }
            return $string;
        }
        return db_connection::instance()->quote($string);
    }
    
    /**
     * Quotes an identifier so it is ready to use in a query.
     *
     * @param   string  $string the string to quote
     * @param   string  $db     the database connection to use
     * @return  string  the quoted identifier
     */
    public static function quote_identifier($string)
    {
        if (is_array($string))
        {
            foreach ($string as $k => $s)
            {
                $string[$k] = static::quote_identifier($s);
            }
            return $string;
        }
        return db_connection::instance()->quote_identifier($string);
    }

    
    /**
     * Quote a database table name and adds the table prefix if needed.
     *
     * @param   string  $string the string to quote
     * @param   string  $db     the database connection to use
     * @return  string  the quoted identifier
     */
    public static function quote_table($string)
    {
        if (is_array($string))
        {
            foreach ($string as $k => $s)
            {
                $string[$k] = static::quote_table($s);
            }
            return $string;
        }
        return db_connection::instance()->quote_table($string);
    }
    
    /**
     * Escapes a string to be ready for use in a sql query
     *
     * @param   string  $string the string to escape
     * @param   string  $db     the database connection to use
     * @return  string  the escaped string
     */
    public static function escape($string)
    {
        return db_connection::instance()->escape($string);
    }

    /**
     * 返回修正后的sql
     * #PB# 替代db_prefix，如果数据库本身需插入这个字符串，使用#!PB#替代
     *
     *     $table = db::table_prefix('user');
     *     $sql = db::table_prefix('SELECT * FROM #PB#_user');
     *
     * @param string $sql
     *
     * @return string
     */
    public static function table_prefix($table = null)
    {
        return db_connection::instance()->table_prefix($table);
    }

    public static function errno($instance = null) 
    {
        return db_connection::instance($instance)->errno();
    }

    public static function error($instance = null) 
    {
        return db_connection::instance($instance)->error();
    }

    public static function close($name = null) 
    {
        return db_connection::instance()->close($name);
    }

    public static function reconnect($instance = null) 
    {
        return db_connection::instance($instance)->reconnect();
    }

    /**
     * 原始表达式
     * 在查询构建器中使用SQL函数的唯一方法
     *
     *     $expression = db::expr('COUNT(users.id)');
     *
     * @param   string $string 表达式
     * @return  db_expression
     */
    public static function expr($string)
    {
        return new db_expression($string);
    }

    /**
     * Alias expr method
     * 
     * @param mixed $string string 
     * 
     * @return void
     */
    public static function raw($string)
    {
        return self::expr($string);
    }

}
