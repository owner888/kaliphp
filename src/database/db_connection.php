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

namespace kaliphp\database;
use kaliphp\config;
use kaliphp\req;
use kaliphp\log;
use kaliphp\db;
use kaliphp\event;
use Exception;

/**
 * 数据库类
 *
 * @version 2.0
 */
class db_connection
{
    private static $_instance = [];

    /**
     * @var string instance name
     */
    private $_name = null;

    /**
     * @var string instance of db name
     */
    private $_db_name = null;

    /**
     * @var array db config
     */
    private $_config = [];

    /**
     * @var  int  Query type
     */
    protected $_type;

    /**
     * @var  string  SQL statement
     */
    protected $_sql;

    /**
     * @var string  $_table  table
     */
    protected $_table;

    /**
     * 从数据库状态 false 则只用主数据库
     *
     * @var bool  $_enable_slave  enable slave
     */
    protected $_enable_slave = true;

    /**
     * @var array $_columns insert columns
     */
    protected $_columns = array();

    /**
     * @var array  $_values insert  values
     */
    protected $_values = array();

    /**
     * @var array  $_set  insert or update values
     */
    protected $_set = array();

    /**
     * @var array  $_dups update values
     */
    protected $_dups = [];

    /**
     * @var array  $_select  columns to select
     */
    protected $_select = array();

    /**
     * @var bool  $_distinct  whether to select distinct values
     */
    protected $_distinct = false;

    /**
     * @var array  $_from  table name
     */
    protected $_from = array();

    /**
     * @var array  $_where  where statements
     */
    protected $_where = array();

    /**
     * @var array  $_having  having clauses
     */
    protected $_having = array();

    /**
     * @var  array  Quoted query parameters
     */
    protected $_parameters = array();

    /**
     * @var array  $_join  join objects
     */
    protected $_join = array();

    /**
     * @var array  $_on  ON clauses
     */
    protected $_on = array();

    /**
     * @var array  $_group_by  group by clauses
     */
    protected $_group_by = array();

    /**
     * @var array  $_order_by  order by clause
     */
    protected $_order_by = array();

    /**
     * @var  integer  $_max_select_limit
     */
    protected $_max_select_limit = 300;

    /**
     * @var  integer  $_limit
     */
    protected $_limit = null;
    
    /**
     * @var integer  $_offset  offset
     */
    protected $_offset = null;

    /**
     * @var  bool  其他属性
     */
    protected $_atts = [];

    /**
     * @var  bool  Return results as associative arrays or objects
     */
    protected $_as_object = false;

    protected $_as_sql = false;

    protected $_as_row = false;

    protected $_as_field = false;

    protected $_as_result = false;

    /**
     * 每个查询最大重连次数
     * @var integer
     */
    protected $_max_reconnect = 2;

    /**
     * @var \mysqli
     */
    private $_handler;

    /**
     * @var  \mysqli_result  $_result raw result resource
     */
    private $_result;

    public static $config = [];

    //当前实例名称，方便多库使用的时候自定义实例名称
    private static $_instance_name = [];

    //默认数据库名称
    private static $_default_name = 'default';

    public static $rps = array(
        '/*', '--', 'union', 'sleep', 'benchmark', 'load_file', 'outfile',
        'extractvalue', 'updatexml', 'information_schema', 'performance_schema'
    );
    public static $rpt = array(
        '/×', '——', 'ｕｎｉｏｎ', 'ｓｌｅｅｐ', 'ｂｅｎｃｈｍａｒｋ', 'ｌｏａｄ_ｆｉｌｅ', 'ｏｕｔｆｉｌｅ',
        'ｅｘｔｒａｃｔｖａｌｕｅ', 'ｕｐｄａｔｅｘｍｌ', 'ｉｎｆｏｒｍａｔｉｏｎ＿ｓｃｈｅｍａ', 'ｐｅｒｆｏｒｍａｎｃｅ＿ｓｃｈｅｍａ'
    );

    private static $chr2 = null;
    private static $chr3 = null;

    /**
     * 初始化
     */
    public static function _init()
    {
        static::$chr2 = chr(2);
        static::$chr3 = chr(3);

        return static::init_db();
    }

    /**
     * 初始化数据库
     * @param string $name 实例名称
     * @param string $config_file 数据库配置文件名
     * @param bool $default_instance 是否设为默认数据库
     */
    public static function init_db($name = null, $config_file = null, $default_instance = false)
    {
        $name  = self::get_muti_name($name);
		// 引入其他模块的类配置文件(比如 clickhouse.php)
        if ($config_file) 
        {
            @list($config_file, $config_key) = explode(':', $config_file);
        }
		// 默认配置文件
        else
        {
            $config_key  = null;       // 如果使用默认配置，只要在默认配置加一个key,就可以实现加载其他数据库
            $config_file = 'database'; // 加载的数据库配置文件名，可以配置成其他文件
        }

        self::$config[$name] = config::instance($config_file)->get($config_key);

        if ( self::$config[$name] ) 
        {
            self::$config[$name]['config_file'] = $config_file;
        }

        if(!self::$config[$name])
        {
            throw new \Exception("Load {$config_file} fail", 3001);
        }
        else if( isset(self::$config[$name]['host']['master']) )
        {
            $instance_name = self::get_instance_name($name);
            // 第一个为默认数据库
            if( !self::$_instance_name && $default_instance )
            {
                self::$_default_name = $name;
            }

			// 如果没有初始化
            if( !isset(self::$_instance[$instance_name['master']]) )
            {
                // 链接主库
                list($host, $port) = explode(":", self::$config[$name]['host']['master']);
                $config = [
                    'host'          => $host,
                    'port'          => $port,
                    'user'          => self::$config[$name]['user'],
                    'pass'          => self::$config[$name]['pass'],
                    'name'          => self::$config[$name]['name'],
                    'read_timeout'  => self::$config[$name]['read_timeout'] ?? null,
                    'timeout'       => self::$config[$name]['timeout'],
                    'charset'       => self::$config[$name]['charset'],
                    'gcm_len'       => self::$config[$name]['group_concat_max_len'] ?? null,
                ];

                // static::$_instance
                self::instance($instance_name['master'], $config);
                static::$_instance[$instance_name['master']]->_db_name = $name;
				// var_dump(self::instance());
				// exit;

                // 如果配置了从库 链接从库
                if( !empty(self::$config[$name]['host']['slave']) )
                {
                    $slaves = self::$config[$name]['host']['slave'][mt_rand(0, count(self::$config[$name]['host']['slave']) - 1)];
                    list($host, $port) = explode(":", $slaves);
                    $config = array_merge($config, array('host' => $host, 'port' => $port));
                    self::instance($instance_name['slave'], $config);
                }
				// 否则从库使用主库的链接
                else
                {
                    static::$_instance[$instance_name['slave']] = static::$_instance[$instance_name['master']];
                }

                static::$_instance[$instance_name['slave']]->_db_name = $name;
            }

            return $name;
        }

        return false;
    }

    /**
     * 获取当前配置信息
     * @param    string|null $name 实例名称
     * @param    string|null $key  key
     * @return   array            [description]
     */
    public static function get_config(?string $name = null, ?string $key = null)
    {
        $name  = self::get_muti_name($name);
        if ( !isset(self::$config[$name]) ) 
        {
            self::init_db($name);
        }

        $configs = self::$config[$name] ?? [];
        if ( $key ) 
        {
            $configs = $configs[$key] ?? null;
        }

        return $configs;
    }

    /**
     * 获取当前对象的数据库句柄
     * @return object
     */
    private function _handler()
    {
        if ( !$this->_handler || !is_object($this->_handler) )
        {
            if (
                !$this->_config || !isset($this->_config['host']) || !isset($this->_config['user']) || 
                !isset($this->_config['pass']) || !isset($this->_config['name']) || !isset($this->_config['port'])
            )
            {
                throw new Exception('配置有误', 3001);
            }

            if ( isset($this->_config['keep-alive']) && $this->_config['keep-alive'] )
            {
                $this->_config['host'] = 'p:'.$this->_config['host'];
            }

            // 让mysqli extension在用 try catch 可以抓到 query 的异常
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try
            {
                $this->_handler = mysqli_init();
                if ( $this->_config['timeout'] ) 
                {
                    mysqli_options($this->_handler, MYSQLI_OPT_CONNECT_TIMEOUT, $this->_config['timeout']);
                }

                // 非cli下 命令执行超时秒数
                if ( PHP_SAPI !== 'cli' && !empty($this->_config['read_timeout']) ) 
                {
                    if ( !defined('MYSQL_OPT_READ_TIMEOUT') ) 
                    {
                        define('MYSQL_OPT_READ_TIMEOUT', 11);
                    }

                    // 最低是3s
                    mysqli_options($this->_handler, MYSQLI_OPT_READ_TIMEOUT, $this->_config['read_timeout']);
                }

                //$this->_handler = mysqli_connect(
                mysqli_real_connect($this->_handler, 
                    $this->_config['host'], 
                    $this->_config['user'], 
                    $this->_config['pass'], 
                    $this->_config['name'], 
                    $this->_config['port']
                );

                // 设置等待超时时间，重现 MySQL server has gone away，方便调试
                //mysqli_query($this->_handler, "SET WAIT_TIMEOUT = 1");

                // 让int、float 返回正确的类型，而不是返回string
                $this->_handler->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

                mysqli_query($this->_handler, "SET NAMES ".$this->_config['charset']);

                if ( !empty($this->_config['gcm_len']) ) 
                {
                    mysqli_query(
                        $this->_handler, 
                        'SET SESSION group_concat_max_len='.$this->_config['gcm_len']
                    );
                }
            }
            catch (Exception $e)
            {
                $this->_handler  = null;
                throw new Exception(sprintf(
                    '%s[%s:%s]', 
                    $e->getMessage(), 
                    $this->_config['host'], 
                    $this->_config['port']
                ), 3001);
            }
        }

        //标记最近的一次sql所使用的实例名称
        self::$config[$this->_db_name]['current_instance'] = $this->_name;
        return $this->_handler;
    }

    /**
     * 全局切换数据库
     * @param string $name 实例名称
     */
    public static function switch_db($name = null)
    {
        $result = false;
        $instance_name = self::get_instance_name($name);
        foreach($instance_name as $k => $v)
        {
            if( isset(self::$_instance[$v]) )
            {
                self::$_instance_name[$k] = $v;
                self::$_default_name = $name;
                $result = true;
            }
        }

        return $result;
    }

    /**
     * 单例
     * @param string $name
     * @param bool $instance
     * @return db
     */
    public static function instance($name = null, ?array $config = null)
    {
        if ($name === null)
        {
            // Use the default instance name
            $name = !empty(self::$_instance_name['master']) ? 
                self::$_instance_name['master'] : self::get_instance_name(self::$_default_name, 'master');
        }

        if ( ! isset(static::$_instance[$name]))
        {
            static::$_instance[$name] = new static($name, $config);
        }

        return static::$_instance[$name];
    }

    /**
     * 主动关闭连接
     * @param string $name
     */
    public function close($name = null)
    {
        foreach(static::$_instance as $_name => $_instance)
        {
            if (!$name || $name == $_name) 
            {
                is_object($_instance->_handler) && @mysqli_close($_instance->_handler);
                static::$_instance[$_name]->_handler = null;
            }
        }
    }

    /**
     * 重新连接
     */
    public function reconnect()
    {
        self::close($this->_name);
        self::instance($this->_name, $this->_config);
        $this->_handler();
    }

    public function __construct($name, $config)
    {
        // 设置实例名
        $this->_name    = $name;
        $this->_config  = $config;
        $this->_handler = null;

        return $this;
    }
    
    /**
     * 从数据库状态 false 则只用主数据库
     * 
     * @param bool $enable_slave
     * @return void
     */
    public function enable_slave($enable_slave = true)
    {
        $this->_enable_slave = $enable_slave;
    }

    /**
     * Enables or disables selecting only unique columns using "SELECT DISTINCT"
     *
     * @param   boolean  $value  enable or disable distinct columns
     * @return  $this
     */
    public function distinct($value = true)
    {
        $this->_distinct = (bool) $value;

        return $this;
    }

    public function query($sql, array $params = [])
    {
        // Change #PB# to db_prefix
        $sql = $this->table_prefix($sql);

        if (!empty($params)) 
        {
            // 因为compile里面是按k-v结构的数组来替换sql语句的，所以
            // 先处理sql里面的？号然后组合好新的数组传给到parameters方法
            // 如果没有？号直接将params传给parameters
            $i = 0;
            $sql = preg_replace_callback('/\?/', 
                function() use (&$params, &$i) {
                    $params['?'.$i] = $params[$i];
                    unset($params[$i]);
                    return ':?'. $i++;
                }, 
                $sql
            );

            // 去掉$params里面为数字的key
            foreach (array_keys($params) as $key) 
            {
                if (is_numeric($key)) 
                {
                    unset($params[$key]);
                }
            }

            $this->parameters($params);
        }
        elseif (self::$config[$this->_db_name]['safe_test'])
        {
            $sql = $this->filter_sql($sql);
        }

        $this->_sql  = $sql;
        $this->_type = $this->get_type($this->_sql);
        // Save the query for debugging

        return $this;
    }
    
    /**
     * Compile the SQL query and return it.
     *
     * @return  string
     */
    public function compile()
    {
        // 如果不是通过query执行的SQL，下面执行拼凑
        if ( empty($this->_sql)) 
        {
            switch ($this->_type) 
            {
                case db::SELECT:
                    $this->_sql = $this->get_compiled_select();
                    break;
                case db::INSERT:
                    $this->_sql = $this->get_compiled_insert();
                    break;
                case db::UPDATE:
                    $this->_sql = $this->get_compiled_update();
                    break;
                case db::DELETE:
                    $this->_sql = $this->get_compiled_delete();
                    break;
                default:
                    break;
            }
        }
        
        // function bind()、param()、parameters()
        if ( ! empty($this->_parameters))
        {
            // Quote all of the values
            $values = array_map(array($this, 'quote'), $this->_parameters);

            // Replace the values in the SQL
            $this->_sql = $this->tr($this->_sql, $values);
        }

        if ( $this->_as_row || $this->_as_field ) 
        {
            if (!preg_match("/limit/i", $this->_sql))
            {
                $this->_sql = preg_replace("/[,;]$/i", '', trim($this->_sql)) . " LIMIT 1 ";
            }

            if( !empty($this->_atts['lock']) )
            {
                $this->_atts['lock'] = false;//用过一次后释放
                $this->_sql .= " FOR UPDATE";
            }
            else if( !empty($this->_atts['share']) )
            {
                $this->_atts['share'] = false;//用过一次后释放
                $this->_sql .= " LOCK IN SHARE MODE";
            }
        }

        //兼容字段中有复杂计算不替换#PB#的情况
        $this->_sql = $this->table_prefix($this->_sql);
        static::log_query($this->convert_back_sql($this->_sql));
        static::log_query($this->get_prepare_sql($this->_sql), 'prepare_sql');
        return $this->_sql;
    }

    /**
     * Execute the current query on the given database.
     *
     * @param   mixed   $is_master Database master or slave
     * @param   array   $params index
     * @param   mixed   $sql 如果传了，就直接执行这个sql，用于Mysql等待超时重新执行使用
     *
     * @return  object  SELECT queries
     */
    public function execute($is_master = false, $params = [], $sql = null)
    {
        // Compile the SQL query
        $sql        = $sql ? $sql : $this->compile();
        $real_sql   = $this->convert_back_sql($sql);
        $this->_sql = $real_sql;

		// 获取当前实例组
        $instance_name = isset($this->_atts['instance_name']) ? 
            $this->_atts['instance_name'] : self::get_instance_name();
  
        // 用户手动指定使用主数据库 或 从数据库状态不可用
        if ( 
            $is_master === true || 
            (isset($is_master) && $this->_enable_slave === false) || 
            !empty($this->_atts['lock'])
        )
        {
            $_instance_name = $instance_name['master'];
        }
        else
        {
            if ($this->_type === db::SELECT)
            {
                $_instance_name = $instance_name['slave'];
            }
            else
            {
                $_instance_name = $instance_name['master'];
            }
        }

		// echo "{$_instance_name}\n"; echo "{$sql}\n";
		db::$query_db_names[] = $_instance_name;
        $db_conn = self::$_instance[$_instance_name];

        try
        {
            // Start the Query Timer
            $time_start = microtime(true);

            if ( !empty(self::$config[$this->_db_name]['noprepare']) ) 
            {
                // 加 @ 去掉下面两个警告
                // mysqli_query(): MySQL server has gone away
                // mysqli_query(): Error reading result set's header
                $this->_result = @mysqli_query($db_conn->_handler(), $real_sql);
            }
            else
            {
                $stmt = $this->convert_prepare_sql($db_conn, $sql);
                $this->_result = $stmt->get_result();
            }

            // Stop and aggregate the query time results
            $query_time = microtime(true) - $time_start;
            static::log_query($query_time, 'query_time');

            // 触发SQL事件
            event::trigger(onSql, [$real_sql, $_instance_name, round($query_time, 6)]);

            // 记录慢查询
            if ( self::$config[$this->_db_name]['slow_query'] && ($query_time > self::$config[$this->_db_name]['slow_query']) )
            {
                log::warning(sprintf('Slow Query [%s]: %s (%ss)', $_instance_name, $real_sql, round($query_time, 6)));
            }

            $result = [];
            if ($this->_type === db::SELECT)
            {
                if ( $this->_as_result ) 
                {
                    $result = $this->_result;
                }
                else 
                {
                    $rows = [];
                    while ($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC))
                    {
                        if ( !empty($this->_atts['row_fn']) )
                        {
                            call_user_func_array($this->_atts['row_fn'], [$row, &$rows]);
                        }
                        else if( empty($params['index']) ) 
                        {
                            $rows[] = $row;
                        }
                        else 
                        {
                            $rows[$row[$params['index']]] = $row;
                        }
                    }

                    mysqli_free_result($this->_result);
                    if ( empty($rows[0]) && empty($params['index']) ) 
                    {
                        $result = [];
                    }
                    elseif ( $this->_as_field ) 
                    {
                        $tmp    = reset($rows);
                        $result = reset($tmp);
                        unset($tmp);
                    }
                    elseif ( $this->_as_row ) 
                    {
                        $result = $rows[0];
                    }
                    else 
                    {
                        $result = $rows;
                    }
                }
            }
            elseif ($this->_type === db::INSERT)
            {
                // Return a list of insert id and rows created
                $result = array(
                    mysqli_insert_id($db_conn->_handler()),
                    mysqli_affected_rows($db_conn->_handler()),
                );

                self::log_query($result[1], 'insert');
            }
            elseif ($this->_type === db::UPDATE or $this->_type === db::DELETE)
            {
                // Return the number of rows affected
                $result = mysqli_affected_rows($db_conn->_handler());

                self::log_query($result, $this->_type === db::UPDATE ? 'update' : 'delete');
            }

        }
        catch (Exception $e)
        {
            $errno  = $e->getCode();
            $errmsg = $e->getMessage();
            log::error(sprintf("%s:%s [%s]", $errno, $errmsg, $real_sql.'('.$this->_db_name.')'), 'SQL Error');
            
            $this->_atts['reconnect_times'] = isset($this->_atts['reconnect_times']) ? ++$this->_atts['reconnect_times'] : 1;
            // Mysql 等待超时,如果是开启了事务，不应该重试，因为重连可能导致事务id发生变化
            if ( 
                empty($this->_atts['start']) && in_array($errno, [2013, 2006]) &&
                //每个查询超出最大重连次数，不再重连，防止触发max_connect_errors，无法连接数据库
                $this->_atts['reconnect_times'] <= $this->_max_reconnect
            ) 
            {
                log::error(sprintf("%s:%s [%s]", $errno, $errmsg, $real_sql.'('.$this->_db_name.')'), 'SQL Reconnect');
                // 重新链接，$this 默认是 default_w 的
                // $this->reconnect();
                $db_conn->reconnect();
                // 再次执行当前方法
                return $this->execute($is_master, $params, $sql);
            }
            // 死锁重试
            else if( 
                in_array($errno, [1213, 1205]) && 
                !empty($this->_atts['delay']) &&
                //每个查询超出最大重连次数，不再重连，防止触发max_connect_errors，无法连接数据库         
                $this->_atts['reconnect_times'] <= $this->_max_reconnect
            )
            {
                if ( $this->_atts['delay'] > 1 ) 
                {
                    usleep($this->_atts['delay']);
                }

                log::warning($errmsg, 'Deadlock Retry');
                return $this->execute($is_master, $params, $sql);
            }

            //如果发生错误，应该重置，否则会发生不可预见的问题
            $this->reset();

            // 没有设置忽略错误
            if ( empty($this->_atts['ignore']) ) 
            {
                log::error(sprintf("%s [%s]", $errmsg, $real_sql), 'SQL Error');
                $tracemsg = $this->get_exception_trace($e);
                throw new Exception($tracemsg);
            }

            return null;
        }

        $this->reset();
        return $result;
    }

    /**
     * Choose the columns to select from.
     *
     * @param   mixed   $select can be a string or array
     *
     * @return  $this
     */
    public function select($select = '*')
    {
        $this->_type = db::SELECT;

        if (is_string($select))
        {
            $select = explode(',', $select);
        }
        elseif (is_object($select))
        {
            $this->_select[] = $select;
        }

        foreach ($select as $val)
        {
            if ($val !== '')
            {
                $this->_select[] = $val;
            }
        }

        return $this;
    }

    /**
     * Generates the FROM portion of the query
     *
     * @param   mixed   $from   can be a string or array
     * @return  $this
     */
    public function from($tables)
    {
        if (is_string($tables))
        {
            $tables = explode(',', $tables);
        }

        foreach ($tables as $val)
        {
            if ($val !== '')
            {
                $this->_from[] = $this->table_prefix($val);
            }
        }

        $this->_table = $this->_from[0];
        return $this;
    }
    
    /**
     * Alias of and_where()
     *
     * @return  $this
     */
    public function where()
    {
        return call_user_func_array(array($this, 'and_where'), func_get_args());
    }

    public function and_where($column, $op = null, $value = null)
    {
        if (empty($column)) 
        {
            return $this;
        }

        if (is_array($column))
        {
            foreach ($column as $key => $val)
            {
                if (is_array($val))
                {
                    if ( isset($val[3]) && strtoupper($val[3]) == 'OR') 
                    {
                        $this->or_where($val[0], $val[1], $val[2]);
                    }
                    else 
                    {
                        $this->and_where($val[0], $val[1], $val[2]);
                    }
                }
                else
                {
                    $this->and_where($key, '=', $val);
                }
            }
        }
        else
        {
            if(func_num_args() === 2)
            {
                $value = $op;
                $op = '=';
            }
            $this->_where[] = array('AND' => array($column, $op, $value));
        }

        return $this;
    }

    /**
     * Creates a new "OR WHERE" condition for the query.
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     *
     * @return  $this
     */
    public function or_where($column, $op = null, $value = null)
    {
        if (is_array($column))
        {
            foreach ($column as $key => $val)
            {
                if (is_array($val))
                {
                    $this->or_where($val[0], $val[1], $val[2]);
                }
                else
                {
                    $this->or_where($key, '=', $val);
                }
            }
        }
        else
        {
            if(func_num_args() === 2)
            {
                $value = $op;
                $op = '=';
            }
            $this->_where[] = array('OR' => array($column, $op, $value));
        }

        return $this;
    }

    /**
     * Alias of and_where_open()
     *
     * @return  $this
     */
    public function where_open()
    {
        return $this->and_where_open();
    }

    /**
     * Opens a new "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_open()
    {
        $this->_where[] = array('AND' => '(');
        return $this;
    }

    /**
     * Opens a new "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function or_where_open()
    {
        $this->_where[] = array('OR' => '(');

        return $this;
    }

    /**
     * Closes an open "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function where_close()
    {
        return $this->and_where_close();
    }

    /**
     * Closes an open "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_close()
    {
        $this->_where[] = array('AND' => ')');
        return $this;
    }

    /**
     * Closes an open "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function or_where_close()
    {
        $this->_where[] = array('OR' => ')');
        return $this;
    }

    /**
     * Applies sorting with "ORDER BY ..."
     *
     * @param   mixed   $column     column name or array($column, $alias) or object
     * @param   string  $direction  direction of sorting
     *
     * @return  $this
     */
    public function order_by($column, $direction = null)
    {
        if (empty($column)) 
        {
            return $this;
        }

        if (is_array($column))
        {
            foreach ($column as $val)
            {
                $value = $val[0];
                $op = empty($val[1]) ? '' : $val[1];
                $this->_order_by[] = array($value, $op);
            }
        }
        else 
        {
            $this->_order_by[] = array($column, $direction);
        }

        return $this;
    }

    /**
     * 设置最大limit
     * 特殊情况下可以单独调用
     * @param    [type]     $number [description]
     * @return   [type]             [description]
     */
    public function max_select_limit(int $number)
    {
        $this->_max_select_limit = $number;
        return $this;
    }

    /**
     * Return up to "LIMIT ..." results
     *
     * @param   integer  $number  maximum results to return
     *
     * @return  $this
     */
    public function limit($number)
    {
        $this->_limit = (int) $number;
        return $this;
    }

    /**
     * Adds addition tables to "JOIN ...".
     *
     * @param   mixed   $table  column name or array($column, $alias)
     * @param   string  $type   join type (LEFT, RIGHT, INNER, etc)
     *
     * @return  $this
     */
    public function join($table, $type = NULL)
    {
        if (!empty($type))
        {
            $type = strtoupper(trim($type));

            if ( ! in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
            {
                $type = '';
            }
            else
            {
                $type .= ' ';
            }
        }

        // Assemble the JOIN statement
        $table = $this->table_prefix($table);
        $table = $this->quote_identifier($table);
        $this->_join[] = $type.'JOIN '.$table;

        return $this;
    }

    /**
     * Adds "ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1  column name or array($column, $alias) or object
     * @param   string  $op  logic operator
     * @param   mixed   $c2  column name or array($column, $alias) or object
     *
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        $joins = $this->_join;
        // 将内部指针指向数组中的最后一个元素
        end($joins);
        // 返回数组内部指针当前指向元素的键名
        $key = key($joins);

        $this->_on[$key][] = array($c1, $op, $c2, 'AND');
        return $this;
    }


    /**
     * Adds "AND ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1  column name or array($column, $alias) or object
     * @param   string  $op  logic operator
     * @param   mixed   $c2  column name or array($column, $alias) or object
     *
     * @return  $this
     */
    public function and_on($c1, $op, $c2)
    {
        return $this->on($c1, $op, $c2);
    }

    /**
     * Adds "OR ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1  column name or array($column, $alias) or object
     * @param   string  $op  logic operator
     * @param   mixed   $c2  column name or array($column, $alias) or object
     *
     * @return  $this
     */
    public function or_on($c1, $op, $c2)
    {
        $joins = $this->_join;
        // 将内部指针指向数组中的最后一个元素
        end($joins);
        // 返回数组内部指针当前指向元素的键名
        $key = key($joins);

        $this->_on[$key][] = array($c1, $op, $c2, 'OR');
        return $this;
    }


    /**
     * Creates a "GROUP BY ..." filter.
     *
     * @param   mixed  $columns  column name or array($column, $column) or object
     * @param   ...
     *
     * @return  $this
     */
    public function group_by($columns)
    {
        $columns = func_get_args();
        foreach($columns as $idx => $column)
        {
            // if an array of columns is passed, flatten it
            if (is_array($column))
            {
                foreach($column as $c)
                {
                    $columns[] = $c;
                }
                unset($columns[$idx]);
            }
        }

        $this->_group_by = array_merge($this->_group_by, $columns);

        return $this;
    }

    /**
     * Alias of and_having()
     *
     * @param   mixed  $column column name or array($column, $alias) or object
     * @param   string $op     logic operator
     * @param   mixed  $value  column value
     *
     * @return  $this
     */
    public function having($column, $op = null, $value = null)
    {
        return call_user_func_array(array($this, 'and_having'), func_get_args());
    }

    /**
     * Creates a new "AND HAVING" condition for the query.
     *
     * @param   mixed  $column column name or array($column, $alias) or object
     * @param   string $op     logic operator
     * @param   mixed  $value  column value
     *
     * @return  $this
     */
    public function and_having($column, $op = null, $value = null)
    {
        if(func_num_args() === 2)
        {
            $value = $op;
            $op = '=';
        }

        $this->_having[] = array('AND' => array($column, $op, $value));

        return $this;
    }

    /**
     * Creates a new "OR HAVING" condition for the query.
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     *
     * @return  $this
     */
    public function or_having($column, $op = null, $value = null)
    {
        if(func_num_args() === 2)
        {
            $value = $op;
            $op = '=';
        }

        $this->_having[] = array('OR' => array($column, $op, $value));

        return $this;
    }

    /**
     * Alias of and_having_open()
     *
     * @return  $this
     */
    public function having_open()
    {
        return $this->and_having_open();
    }

    /**
     * Opens a new "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_open()
    {
        $this->_having[] = array('AND' => '(');

        return $this;
    }

    /**
     * Opens a new "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function or_having_open()
    {
        $this->_having[] = array('OR' => '(');

        return $this;
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function having_close()
    {
        return $this->and_having_close();
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_close()
    {
        $this->_having[] = array('AND' => ')');

        return $this;
    }

    /**
     * Closes an open "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function or_having_close()
    {
        $this->_having[] = array('OR' => ')');

        return $this;
    }

    /**
     * Set the value of a parameter in the query.
     *
     * @param   string $param parameter key to replace
     * @param   mixed  $value value to use
     *
     * @return  $this
     */
    public function param($param, $value)
    {
        // Add or overload a new parameter
        $this->_parameters[$param] = $value;

        return $this;
    }

    /**
     * Bind a variable to a parameter in the query.
     *
     * @param  string $param parameter key to replace
     * @param  mixed  $var   variable to use
     *
     * @return $this
     */
    public function bind($param, & $var)
    {
        // Bind a value to a variable
        $this->_parameters[$param] =& $var;

        return $this;
    }

    public function sql($real = false)
    {
        return $this->get_compiled_sql($real);
    }

    public function get_compiled_sql($real = false)
    {
        // Compile the SQL query
        $sql = $this->compile();
        $this->reset();
        return $real ?  $this->convert_back_sql($sql) : $sql;
    }

    public function get_compiled_select()
    {
        // Callback to quote identifiers
        $quote_ident = [$this, 'quote_identifier'];

        // Callback to quote tables
        $quote_table = [$this, 'quote_table'];

        // Callback to quote tables
        $quote_field = [$this, 'quote_field'];

        // Start a selection query
        $sql = 'SELECT ';

        if ( $this->_distinct === TRUE )
        {
            // Select only unique results
            $sql .= 'DISTINCT ';
        }

        if ( empty($this->_select) )
        {
            // Select all columns
            $sql .= '*';
        }
        else
        {
            $sql .= implode(', ', array_unique(array_map($quote_field, $this->_select)));
        }

        if ( !empty($this->_atts['union']) && !empty($this->_atts['is_union_table']) ) 
        {
            $union_sql = null;
            foreach($this->_atts['union'] as $v)
            {
                $union_sql .= !$union_sql ? 
                sprintf('(%s) ', $v['sql']) : 
                sprintf(' UNION %s (%s)', ($v['type'] ? ' ALL ' : ''), $v['sql']);
            }

            $sql .= sprintf(
                ' FROM (%s) %s', 
                $union_sql, 
                is_bool($this->_atts['is_union_table']) ? 't' . uniqid() : $this->_atts['is_union_table']
            );
        }
        else if ( ! empty($this->_from))
        {
            // Set tables to select from
            $sql .= ' FROM '.implode(', ', array_unique(array_map($quote_table, $this->_from)));
        }

        if( !empty($this->_atts['index_name']) )
        {
            $sql .= ' FORCE INDEX('.$this->_atts['index_name'].')';
            $this->_atts['index_name'] = '';
        }

        if ( ! empty($this->_join))
        {
            // Add tables to join[$table]
            $sql .= ' '.$this->_compile_join($this->_join);
        }

        if ( ! empty($this->_where))
        {
            // Add selection conditions
            $sql .= ' WHERE '.$this->_compile_conditions($this->_where);
        }

        if ( ! empty($this->_group_by))
        {
            // Add sorting
            $sql .= ' GROUP BY '.implode(', ', array_map($quote_ident, $this->_group_by));
        }

        if ( ! empty($this->_having))
        {
            // Add filtering conditions
            $sql .= ' HAVING '.$this->_compile_conditions($this->_having);
        }

        if ( !empty($this->_atts['union']) && empty($this->_atts['is_union_table']) ) 
        {
            foreach($this->_atts['union'] as $v)
            {
                $sql .= sprintf(' UNION %s (%s)', ($v['type'] ? ' ALL ' : ''), $v['sql']);
            }
        }

        if ( ! empty($this->_order_by))
        {
            // Add sorting
            $sql .= ' '.$this->_compile_order_by($this->_order_by);
        }

        if ( $this->_as_row || $this->_as_field ) 
        {
            $this->_limit = 1;   
        }

        // select 查询 limit 需要做下限制
        if (PHP_SAPI !== 'cli') 
        {
            $this->_limit = empty($this->_limit) ? $this->_max_select_limit : min($this->_limit, $this->_max_select_limit);
        }

        if ( ! empty($this->_limit))
        {
            // Add limiting
            $sql .= ' LIMIT '.$this->_limit;
        }

        if ($this->_offset !== NULL)
        {
            // Add offsets
            $sql .= ' OFFSET '.$this->_offset;
        }

        if(  !empty($this->_atts['lock']) && empty($this->_as_row) ) 
        {
            $this->_atts['lock'] = false;//用过一次后释放
            $sql .= ' FOR UPDATE';
        }
        else if(  !empty($this->_atts['share']) && empty($this->_as_row) ) 
        {
            $this->_atts['share'] = false;//用过一次后释放
            $sql .= ' LOCK IN SHARE MODE';
        }

        return $sql;
    }

    private function _get_compiled_atts()
    {
        $delay_maps = [
            1 => ' LOW_PRIORITY ',
            2 => $this->_type ==  db::UPDATE ? ' LOW_PRIORITY ' : ' DELAYED ',
        ];

        $delay  = !empty($this->_atts['delay']) && $this->_type != db::DELETE ? 
                 ($delay_maps[$this->_atts['delay']] ?? '')  : '';
        $ignore = !empty($this->_atts['ignore']) ? ' IGNORE ' : '';

        return $delay . $ignore;
    }

    public function get_compiled_insert()
    {
        $table = $this->table_prefix($this->_table);
        // Start an insertion query
        $sql   = 'INSERT ' . $this->_get_compiled_atts() .' INTO '.$table;

        //因为json字段初始化不能为空，否则后面是没发更新的，必须给他一个默认值{}
        if ( !empty(self::$config[$this->_db_name]['json_fields'][$table]) ) 
        {
            foreach (self::$config[$this->_db_name]['json_fields'][$table] as $field) 
            {
                if ( !in_array($field, $this->_columns) ) 
                {
                    $this->columns([$field]);
                    $this->_values = array_map(function($item) use ($field) {
                        $item[$field] = json_encode(new \stdClass);
                        return $item;
                    }, $this->_values);
                }
            }
        }

        // Add the column names
        $sql .= ' ('.implode(', ', array_map(array($this, 'quote_identifier'), $this->_columns)).') ';

        if (is_array($this->_values))
        {
            // Callback for quoting values
            //$quote = array($this, 'quote');
            $quote  = array($this, 'quote_value');

            $groups = array();
            $fields = array_keys(reset($this->_values));
            foreach ($this->_values as $group_item)
            {
                $group = [];
                foreach ($fields as $i)
                {
                    //$value = $group_item[$i] ?? (is_numeric($this->_values[0][$i]) ? 0 : '');
                    $value = $group_item[$i] ?? null;
                    if (is_string($value) AND isset($this->_parameters[$value]))
                    {
                        // Use the parameter value
                        $group[$i] = $this->_parameters[$value];
                    }

                    if (is_string($i))
                    {
                        $field = $i;
                    }
                    else 
                    {
                        $field = $this->_columns[$i];
                    }

                    $group[$i] = array($value, $field);
                    //$group[$i] = $value;
                }

                //$val = '('.implode(', ', array_map($quote, $group)).')';
                //$val = str_replace("'AES_ENCRYPT", "AES_ENCRYPT", $val);
                //$val = str_replace("')'", "')", $val);
                //$groups[] = $val;
                $groups[] = '('.implode(', ', array_map($quote, $group)).')';
            }
   
            // Add the values
            $sql .= 'VALUES '.implode(', ', $groups);
        }
        else
        {
            // Add the sub-query
            $sql .= (string) $this->_values;
        }

        if ( !empty($this->_dups) )
        {
            $sql .= ' ON DUPLICATE KEY  UPDATE '.$this->_compile_dups($this->_dups);
            $this->_dups = [];
        }

        return $sql;
    }

    /**
     * 设置批量更新字段
     * @param    string     $field 
     */
    public function set_update_cmp_field(string $field)
    {
        $this->_atts['update_cmp_field'] = $field;
        return $this;
    }

    public function get_compiled_update()
    {
        // Start an update query
        $sql = 'UPDATE '. $this->_get_compiled_atts() .
            $this->table_prefix($this->_table);
        if ( ! empty($this->_join))
        {
            // Add tables to join
            $sql .= ' '.$this->_compile_join($this->_join);
        }

        //批量更新
        if ( isset($this->_atts['update_cmp_field']) ) 
        {
            $sql .= ' SET '.$this->_compile_batch_update_set($this->_set);
        }
        else
        {
            // Add the columns to update
            $sql .= ' SET '.$this->_compile_set($this->_set);
        }

        if ( ! empty($this->_where))
        {
            // Add selection conditions
            $sql .= ' WHERE '.$this->_compile_conditions($this->_where);
        }

        if ( ! empty($this->_order_by))
        {
            // Add sorting
            $sql .= ' '.$this->_compile_order_by($this->_order_by);
        }

        if ( ! empty($this->_limit))
        {
            // Add limiting
            $sql .= ' LIMIT '.$this->_limit;
        }

        return $sql;
    }

    public function get_compiled_delete()
    {
        // Start a deletion query
        $sql = 'DELETE ' .$this->_get_compiled_atts(). ' FROM ' .$this->table_prefix($this->_table);

        if ( ! empty($this->_where))
        {
            // Add deletion conditions
            $sql .= ' WHERE '.$this->_compile_conditions($this->_where);
        }

        if ( ! empty($this->_order_by))
        {
            // Add sorting
            $sql .= ' '.$this->_compile_order_by($this->_order_by);
        }

        if ( ! empty($this->_limit))
        {
            // Add limiting
            $sql .= ' LIMIT '.$this->_limit;
        }

        return $sql;
    }

    // 暂时没用
    public function get_fields($table)
    {
        // $sql = "SHOW COLUMNS FROM $table"; //和下面的语句效果一样
        $rows   = $this->query("Desc `{$table}`")->execute();
        $fields = array();
        foreach ($rows as $v)
        {
            // 过滤自增主键
            // if ($v['Key'] != 'PRI')
            if ($v['Extra'] != 'auto_increment')
            {
                $fields[] = $v['Field'];
            }
        }

        return $fields;
    }

    //-------------------------------------------------------------
    // INSERT
    //-------------------------------------------------------------
    public function insert($table = null, ?array $columns = null)
    {
        $this->_type = db::INSERT;

        if ($table)
        {
            // Set the initial table name
            $this->_table = $this->table_prefix($table);
        }

        if ($columns)
        {
            // Set the column names
            $this->_columns = $columns;
        }

        return $this;
    }

    
    /**
     * Set the columns that will be inserted.
     *
     * @param   array $columns column names
     * @return  $this
     */
    public function columns(array $columns)
    {
        $this->_columns = array_merge($this->_columns, $columns);
        return $this;
    }

    /**
     * Adds values. Multiple value sets can be added.
     *
     * @throws Exception
     * @param array $values
     * @return $this
     */
    public function values(array $values)
    {
        if ( ! is_array($this->_values))
        {
            throw new Exception('INSERT INTO ... SELECT statements cannot be combined with INSERT INTO ... VALUES');
        }

        // Get all of the passed values
        $values = func_get_args();
        // And process them
        foreach ($values as $value)
        {
            $keys = array_keys($value);
            //有可能第一个key是json中的,如果批量插入，key为数组
            if ( is_array(reset($value)) && !$this->_check_json_field(reset($keys)) )
            {

                $this->_values = array_merge($this->_values, $value);
            }
            else
            {
                $this->_values[] = $value;
            }
        }
 
        return $this;
    }

    /**
     * This is a wrapper function for calling columns() and values().
     *
     * @param array $pairs column value pairs
     *
     * @return  $this
     */
    public function set(array $pairs)
    {
        if ($this->_type == db::INSERT) 
        {
            // 把key存到 _columns 里面
            $this->columns(array_keys($pairs));
            // 把值存到 _values 里面
            $this->values($pairs);
        }
        elseif ($this->_type == db::UPDATE) 
        {
            foreach ($pairs as $column => $value)
            {
                $this->_set[] = [$column, $value];
            }
        }

        return $this;
    }

    //-------------------------------------------------------------
    // UPDATE
    //-------------------------------------------------------------
    public function update($table = null)
    {
        $this->_type = db::UPDATE;

        if ($table)
        {
            // Set the initial table name
            $this->_table = $this->table_prefix($table);
        }

        return $this;
    }

    /**
     * Set the value of a single column.
     *
     * @param   mixed  $column  field name or [$table, $alias] or object
     * @param   mixed  $value   column value
     *
     * @return  $this
     */
    public function value($column, $value)
    {
        $this->_set[] = [$column, $value];

        return $this;
    }

    //-------------------------------------------------------------
    // DELETE
    //-------------------------------------------------------------
    public function delete($table = null)
    {
        $this->_type = db::DELETE;

        if ($table)
        {
            // Set the initial table name
            $this->_table = $this->table_prefix($table);
        }

        return $this;
    }

    /**
     * Compiles an array of JOIN statements into an SQL partial.
     *
     * @param   object $db    Database instance
     * @param   array  $joins join statements
     *
     * @return  string
     */
    protected function _compile_join(array $joins)
    {
        foreach (array_keys($joins) as $key)
        {
            $conditions = array();

            foreach ($this->_on[$key] as $condition)
            {
                // Split the condition
                list($c1, $op, $c2, $chaining) = $condition;

                // Add chain type
                $conditions[] = ' '.$chaining.' ';

                if ($op)
                {
                    // Make the operator uppercase and spaced
                    $op = ' '.strtoupper($op);
                }

                // Quote each of the identifiers used for the condition
                $c1 = $this->quote_identifier($c1);
                if ( is_array($c2) && $op != '=' ) 
                {
                    $c2 = $this->quote($c2);
                }
                else
                {
                    $c2 = $this->quote_identifier($c2);
                }

                $conditions[] = $c1.$op.' '.(is_null($c2) ? 'NULL' : $c2);
            }

            // remove the first chain type
            array_shift($conditions);

            // if there are conditions, concat the conditions "... AND ..." and glue them on...
            empty($conditions) or $joins[$key] .= ' ON ('.implode('', $conditions).')';

        }

        $sql = implode(' ', $joins);
        return $sql;
    }

    /**
     * 返回匹配边界符，方便prepare替换
     * @param  mixed $value 
     * @return string      
     */
    protected function _get_chr_value($value)
    {
        // 全部转成string
        return sprintf(
            '%s\'%s\'%s', 
            static::$chr2, 
            str_replace([static::$chr2, static::$chr3], '', $value), 
            static::$chr3
        );
    }

    /**
     * 返回匹配规则
     * @param    boolean    $filter_quote
     * @return   string        
     */
    protected function _get_chr_pattern($width_quote = false)
    {
        $quote = $width_quote ? '\'' : '';
        return sprintf(
            '#%s%s(?<where_val>.*)%s%s#sU', 
            static::$chr2,
            $quote, 
            $quote, 
            static::$chr3
        );
    }

    /**
     * 返回真实的sql
     * @param   string $sql
     * @return  string
     */
    protected function convert_back_sql(string $sql)
    {
        return preg_replace_callback($this->_get_chr_pattern(), function($matches) {
            return $matches[1];
        }, $sql);
    }

    protected function get_prepare_sql(
        $sql, 
        array &$params = [], 
        string &$types = ''
    )
    {
        $prepared_sql = preg_replace_callback(
            $this->_get_chr_pattern(true), 
            function($matches) use (&$params, &$types) {
                $val = $matches['where_val'];
                $params[] = $val;
                $types   .= 's';
                return '?';
            }, 
            $sql
        );

        return $prepared_sql;
    }

    /**
     * 返回 prepare stmt
     * @param  object $db_conn
     * @param  string $sql    
     * @return mysqli_stmt 
     */
    protected function convert_prepare_sql($db_conn, $sql) 
    {
        $params = [];
        $types  = '';
        // 使用 preg_replace_callback 函数将值替换为 ? 占位符，并将值存储在数组中
        // bind_param的时候不能根据参数自动判断类型，因为数据库类型是varchar/char的时候这个时候给一个int,模版是i
        // 会导致索引失效，甚至导致返回错误数据，比如搜索123的时候，会把123xxxx查询处理，因为给他i,他会把数据库中的字段转成int
        // 索引模版直接全部给s,就不会出现因为类型错误，导致索引失效/数据错误的bug
        $prepared_sql = $this->get_prepare_sql($sql, $params, $types);

        // 使用 mysqli_prepare 函数创建 prepared statement
        $stmt = mysqli_prepare($db_conn->_handler(), $prepared_sql);
        // 检查创建 prepared statement 是否成功
        if ( !$stmt ) 
        {
            throw new Exception(mysqli_error($db_conn->_handler()) . sprintf('[%s]', $prepared_sql));
        }

        // 绑定参数
        if ( $params ) 
        {
            // 使用 call_user_func_array 函数将参数绑定到 prepared statement
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        // 返回 prepared statement
        return $stmt;
    }

    /**
     * Compiles an array of conditions into an SQL partial. Used for WHERE
     * and HAVING.
     *
     * @param   object $db         Database instance
     * @param   array  $conditions condition statements
     *
     * @return  string
     */
    protected function _compile_conditions(array $conditions)
    {
        $last_condition = NULL;
        $conditions     = $conditions ?? $this->_where;

        $sql = '';
        foreach ($conditions as $group)
        {
            // Process groups of conditions
            foreach ($group as $logic => $condition)
            {
                if ($condition === '(')
                {
                    if ( ! empty($sql) AND $last_condition !== '(')
                    {
                        // Include logic operator
                        $sql .= ' '.$logic.' ';
                    }

                    $sql .= '(';
                }
                elseif ($condition === ')')
                {
                    $sql .= ')';
                }
                else
                {
                    if ( ! empty($sql) AND $last_condition !== '(')
                    {
                        // Add the logic operator
                        $sql .= ' '.$logic.' ';
                    }

                    // Split the condition
                    list($column, $op, $value) = $condition;
                    // Support db::expr() as where clause
                    if ($column instanceOf db_expression and $op === null and $value === null)
                    {
                        $sql .= (string) $column;
                    }
                    else
                    {
                        if ($value === NULL)
                        {
                            if ($op === '=')
                            {
                                // Convert "val = NULL" to "val IS NULL"
                                $op = 'IS';
                            }
                            elseif ($op === '!=')
                            {
                                // Convert "val != NULL" to "valu IS NOT NULL"
                                $op = 'IS NOT';
                            }
                        }

                        // Database operators are always uppercase
                        $op = strtoupper($op);
                        if (($op === 'BETWEEN' OR $op === 'NOT BETWEEN') AND is_array($value))
                        {
                            // BETWEEN always has exactly two arguments
                            list($min, $max) = $value;

                            if (is_string($min) AND array_key_exists($min, $this->_parameters))
                            {
                                // Set the parameter as the minimum
                                $min = $this->_parameters[$min];
                            }

                            if (is_string($max) AND array_key_exists($max, $this->_parameters))
                            {
                                // Set the parameter as the maximum
                                $max = $this->_parameters[$max];
                            }

                            // Quote the min and max value
                            $value = $this->quote($min).' AND '.$this->quote($max);
                        }
                        elseif ($op === 'FIND_IN_SET' || strstr($column, '->') )
                        {
                        }
                        else
                        {
                            if (is_string($value) AND array_key_exists($value, $this->_parameters))
                            {
                                // Set the parameter as the value
                                $value = $this->_parameters[$value];
                            }

                            // Quote the entire value normally
                            $value = $this->quote($value);
                        
                        }

                        //json字段查询
                        if ( strstr($column, '->') ) 
                        {
                            $value = is_string($value) ? $this->quote($value) : $value;
                            list($column, $json_field) = explode('->', $column, 2);

                            $column = $this->quote_field($column, false);
                            $sql .= $column.'->\'$.' . $json_field . '\' '.$op.' '.$value;
                        }
                        else
                        {
                            // Append the statement to the query
                            $column = $this->quote_field($column, false);
                            if ($op === 'FIND_IN_SET') 
                            {
                                $sql .= $op."( '{$value}', {$column} )";
                            }
                            else 
                            {
                                $sql .= $column.' '.$op.' '.$value;
                            }
                        }
                    }
                }

                $last_condition = $condition;
            }
        }

        return $sql;
    }

    
    /**
     * Compiles an array of set values into an SQL partial. Used for UPDATE.
     *
     * @param   object $db     Database instance
     * @param   array  $values updated values
     *
     * @return  string
     */
    protected function _compile_set(array $values)
    {
        $set = [];
        foreach ($values as $group)
        {
            // Split the set
            list($column, $value) = $group;

            if (is_string($value) AND array_key_exists($value, $this->_parameters))
            {
                // Use the parameter value
                $value = $this->_parameters[$value];
            }

            $value = $this->quote_value([$value, $column]);
            $column = $this->quote_identifier($column);

            // Quote the column name
            $set[$column] = $column.' = '.$value;
        }

        return implode(', ', $set);
    }

    /**
     * 批量更新
     * @param    array      $values
     * @return   string           
     */
    protected function _compile_batch_update_set(array $values)
    {
        $set  = $update_fields = $pk_vals = [];
        $pk   = $this->_atts['update_cmp_field'];
        $data = array_column($values, 1);

        //先获取更新字段对应的pk和更新值
        foreach ($data as $value) 
        {
            //每个数组必须带有pk
            if ( !is_array($value) || !isset($value[$pk]) ) 
            {
                throw new Exception('unKnown', 3002);
            }

            $pk_vals[$value[$pk]] = $value[$pk];
            foreach ($value as $k => $v) 
            {
                if ( $k != $pk ) 
                {
                    $update_fields[$k][$value[$pk]] = $v;
                }
            }
        }
        
        //按字段进行更新，这样避免批量更新不同字段的而当成一样的问题
        foreach ( $update_fields as $field => $val )
        {
            $tmp = $this->quote_identifier($field) . " = (CASE {$pk}";
            foreach ( $val as $k => $v )
            {
                $tmp .= sprintf(
                    ' WHEN %s THEN %s ', 
                    $this->quote_value(array($k, $pk)),
                    $this->quote_value(array($v, $pk))
                );
            }

            //不同字段的时候如果没有else,会把缺省的字段设置为null
            $tmp  .= ' ELSE ' . $this->quote_identifier($field) . ' END)';
            $set[] = $tmp;
        }

        $this->and_where($pk, 'IN', $pk_vals);
        return implode(', ', $set);
    }
    /**
     * Compiles an array of set values into an SQL partial. Used for UPDATE.
     *
     * @param   object $db     Database instance
     * @param   array  $values updated values
     *
     * @return  string
     */
    protected function _compile_dups(array $values) 
    {
        $dups = array();
        foreach ($values as $group) {
            // Split the dups
            list($column, $value) = $group;
            if (is_string($value) AND array_key_exists($value, $this->_parameters)) 
            {
                // Use the parameter value
                $value = $this->_parameters[$value];
            }

            // json字段
            if( is_array($value) && false != $this->_check_json_field($column) )
            {
                if ( !$value ) 
                {
                    $value = '\'' . json_encode(new \stdClass) . '\'';
                }
                else
                {
                    $tmp = [$column];
                    foreach($value as $f => $ff)
                    {
                        $ff    = is_array($ff) ? addslashes(json_encode((object)$ff, JSON_UNESCAPED_UNICODE)) : $ff;
                        //string的才加‘’,否则不加
                        // $ff    = is_string($ff) || !$ff ? "'{$ff}'" : $ff;
                        $tmp[] = "'$.\"{$f}\"', " . $this->_get_chr_value($ff);
                    }

                    $value = 'JSON_SET(' . implode(", ", $tmp) . ')';
                }
            }
            // 兼容 `xxx` 和 values(`xxx`)
            else if(!preg_match('#values\s*\([^\)]+\)#i', $value ?? ''))
            {
                $value = $this->quote_value(array($value, $column));
            }

            // Quote the column name
            $column = $this->quote_identifier($column);
            $dups[$column] = $column.' = '.$value;
        }

        return implode(', ', $dups);
    }

    /**
     * 检查某个字段是否在json中
     * @Author han
     * @param  string $column 
     * @return bool   true/false
     */
    private function _check_json_field(string $column) : bool
    {
        if (      
            !empty(self::$config[$this->_db_name]['json_fields'][$this->_table]) && 
            in_array($column, self::$config[$this->_db_name]['json_fields'][$this->_table])
        ) 
        {
            return true;
        }

        return false;
    }

    /**
     * Compiles an array of ORDER BY statements into an SQL partial.
     *
     * @param   object  $db       Database instance
     * @param   array   $columns  sorting columns
     *
     * @return  string
     */
    protected function _compile_order_by(array $columns)
    {
        $sort = array();

        foreach ($columns as $group)
        {
            list($column, $direction) = $group;
            if ( is_object($column) ) 
            {
                $sort[] = $column;
                continue;
            }

            if ( !empty($direction))
            {
                $direction = strtoupper($direction);
                $direction = ' '.($direction == 'ASC' ? 'ASC' : 'DESC');
            }

            $column = $this->quote_identifier($column);
            $column = $this->quote_field($column, false);
            $sort[] = $column.$direction;
        }

        return 'ORDER BY '.implode(', ', $sort);
    }

    /**
     * Parse the params from a string using strtr()
     *
     * @param   string  $string  string to parse
     * @param   array   $array   params to str_replace
     * @return  string
     */
    public function tr($string, $array = array())
    {
        if (is_string($string))
        {
            $tr_arr = array();

            foreach ($array as $from => $to)
            {
                substr($from, 0, 1) !== ':' and $from = ':'.$from;
                $tr_arr[$from] = $to;
            }
            unset($array);

            return strtr($string, $tr_arr);
        }
        else
        {
            return $string;
        }
    }

    /**
     * Add multiple parameters to the query.
     *
     * @param array $params list of parameters
     *
     * @return  $this
     */
    public function parameters(array $params)
    {
        // Merge the new parameters in
        $this->_parameters = $params + $this->_parameters;

        return $this;
    }

    // innodb排他行锁，其他事物不能读和改
    public function lock($value = true) 
    {
        $this->_atts['lock'] = (bool) $value;
        return $this;
    }

    // innodb共享锁，多个事物可以同时获取共享锁，但是大于1个事务同时获取共享锁后，没法更新
    public function share($value = true) 
    {
        $this->_atts['share'] = (bool) $value;
        return $this;
    }

    // 忽略错误
    public function ignore($value = true) 
    {
        $this->_atts['ignore'] =(bool) $value;
        return $this;
    }

    // 强制使用索引，非主键索引，行数占比太多，优化器不会跑索引，而是全表扫描
    // 一些行级锁的操作，使用的是非主键的索引的必须带上，否则会死锁
    public function force_index($index_name)
    {
        if( !empty($index_name) )
        {
            $this->_atts['index_name'] = $index_name;
        }

        return $this; 
    }

    public function set_row_fn(callable $fn)
    {
        $this->_atts['row_fn'] = $fn;
        return $this;
    }

    //innodb排他行锁，其他事物不能读和改
    public function union(string $sql, ?string $type = null) 
    {
        $this->_atts['union'][] = ['sql' => $sql, 'type' => $type];
        return $this;
    }

    // union 当作表，不需要from
    public function is_union_table($is_union_table)
    {
        $this->_atts['is_union_table'] = $is_union_table;
    }

    /**
     * 当前查询指定库
     * @param string $name 实例名称
     * @param string $config_file 实例配置文件名称，如果指定了会尝试初始化
     * @param string $default_db 是否为默认库
     * @return  $this
     */
    public function from_db($name = null, $config_file = null, $default_db = null)
    {
        self::init_db($name, $config_file, $default_db);
        $_instance_name = self::get_instance_name($name);
        if( !isset(self::$_instance[$_instance_name['master']]) )
        {
            throw new Exception("instance:{$name} is not exit", 3001);
        }

        $this->_atts['instance_name'] = $_instance_name;

        return $this;
    }

    /**
     * This is a wrapper function for calling dup().
     * 重复键时批量更新
     * @param array $pairs column value pairs
     *
     * @return  $this
     */
    public function dup(array $pairs) 
    {
        foreach ($pairs as $column => $value) 
        {
            $this->_dups[] = array($column, $value);
        }

        return $this;
    }

    // 主要用于更新或者删除是是否有条件
    public function has_where() 
    {
        foreach(self::$_instance as $instance)
        {
            if( $instance->_where )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * 返回修正后的sql
     * #PB# 替代db_prefix，如果数据库本身需插入这个字符串，使用#!PB#替代
     *
     *     $table = $db->table_prefix('#PB#_user');
     *     $table = $db->table_prefix('SELECT * FROM #PB#_user');
     *
     * @param string $table
     *
     * @return  string
     */
    public function table_prefix($table = null)
    {
        if ($table !== null)
        {
            $table = str_replace('#PB#', self::$config[$this->_db_name]['prefix'] ?? '', trim($table));
            $table = str_replace('#!PB#', '#PB#', $table);
            return $table;
        }

        return self::$config[$this->_db_name]['prefix'];
    }

    public function errno() 
    {
        $_instance_name = self::$config[$this->_db_name]['current_instance'];
        return mysqli_errno(self::$_instance[$_instance_name]->_handler());
    }

    public function error() 
    {
        $_instance_name = self::$config[$this->_db_name]['current_instance'];
        return mysqli_error(self::$_instance[$_instance_name]->_handler());
    }

    public function real_escape_string(string $string)
    {
        return mysqli_real_escape_string($this->_handler(), $string);
    }

    /**
     * Quote a value for an SQL query.
     *
     *     $this->quote(null);   // 'null'
     *     $this->quote(10);     // 10
     *     $this->quote('fred'); // 'fred'
     *
     * @param   mixed $value
     *
     * @return  string
     *
     * @uses    string
     */
    public function quote($value)
    {
        if ($value === null)
        {
            return 'null';
        }
        elseif ($value === true)
        {
            return "'1'";
        }
        elseif ($value === false)
        {
            return "'0'";
        }
        elseif (is_object($value))
        {
            // 未使用，因为query并没有分开到db_query类去
            //if ($value instanceof db_query)
            //{
                //// Create a sub-query
                //return '('.$value->compile($this).')';
            //}
            if ($value instanceof db_expression)
            {
                // Use a raw expression
                $value = $value->value();
            }
            else
            {
                // Convert the object to a string
                $value = (string) $value;
            }

            return $value;
        }
        elseif (is_array($value))
        {
            return '('.implode(', ', array_map(array($this, __FUNCTION__), $value)).')';
        }

        return $this->_get_chr_value($value);
    }

    /**
     * Quote a database table name and adds the table prefix if needed.
     *
     *     $table = $db->quote_table($table);
     *
     * @param   mixed $value table name or array(table, alias)
     *
     * @return  string
     *
     * @uses    static::quote_identifier
     * @uses    static::table_prefix
     */
    public function quote_table($value)
    {
        // Assign the table by reference from the value
        if (is_array($value))
        {
            $table =& $value[0];

            // Attach table prefix to alias
            $value[1] = $this->table_prefix($value[1]);
        }
        else
        {
            $table =& $value;
        }

        // deal with the sub-query objects first
        //if ($table instanceof Database_Query)
        //{
            //// Create a sub-query
            //$table = '('.$table->compile($this).')';
        //}
        if (is_string($table))
        {
            if (strpos($table, '.') === false)
            {
                // Add the table prefix for tables
                $table = $this->quote_identifier($this->table_prefix($table));
            }
            else
            {
                // Split the identifier into the individual parts
                $parts = explode('.', $table);

                if ($this->table_prefix())
                {
                    // Get the offset of the table name, 2nd-to-last part
                    // This works for databases that can have 3 identifiers (Postgre)
                    if (($offset = count($parts)) == 2)
                    {
                        $offset = 1;
                    }
                    else
                    {
                        $offset = $offset - 2;
                    }

                    // Add the table prefix to the table name
                    $parts[$offset] = $this->table_prefix($parts[$offset]);
                }

                // Quote each of the parts
                $table = implode('.', array_map(array($this, 'quote_identifier'), $parts));
            }
        }

        // process the alias if present
        if (is_array($value))
        {
            // Separate the column and alias
            list($value, $alias) = $value;

            return $value.' AS '.$this->quote_identifier($alias);
        }
        else
        {
            // return the value
            return $value;
        }
    }
    
    /**
     * Quote a field value for an SQL query.
     * for method select、where、order by fields
     * 
     * @param mixed $value
     * @param string $select    是否SELECT子句里面的参数，是才能带AS匿名
     * @param string $inside    是否从SUM、MIX、MIN等函数里面提取出来的字段名
     * @return void
     */
    public function quote_field($value, $select = true, $inside = false)
    {
        if ($value === '*')
        {
            return $value;
        }
        elseif (is_object($value))
        {
            // 暂未使用
            //if ($value instanceof db_query)
            //{
                //// Create a sub-query
                //return '('.$value->compile($this).')';
            //}
            if ($value instanceof db_expression)
            {
                // Use a raw expression
                return $value->value();
            }
            else
            {
                // Convert the object to a string
                return $this->quote_identifier((string) $value);
            }
        }
        // 处理Mysql函数
        elseif (strcspn($value, "()'") !== strlen($value))
        {
            // 匹配CONCAT()
            if ( preg_match("#^concat\((.*?)\)#i", $value, $matchs) )
            {
                $match_value = $matchs[1];
                $match_value_arr = explode(",", $match_value);
                $tmp_value_arr = array();
                foreach ($match_value_arr as $v) 
                {
                    $v = trim(str_replace('`', '', $v));
                    $v = $this->quote_identifier($v);
                    $v = $this->quote_field($v);
                    $tmp_value_arr[] = $v;
                }

                $quote_value = implode(", ", $tmp_value_arr);
                $quote_value = preg_replace('#as\s+`[^`]+`#i', '', $quote_value);
                $value = str_ireplace("concat(".$match_value.")", "CONCAT(".$quote_value.")", $value);
            }
            // 匹配空格、tab符号、`符号
            elseif (
                !preg_match('#distinct\s+#i', $value) && 
                preg_match("#\(([ \t\w\`]+)\)#i", $value, $matchs)
            )
            {
                $match_value = $matchs[1];
                $quote_value = $this->quote_field($match_value, $select, true);
                $value = str_replace("(".$match_value.")", "(".$quote_value.")", $value);
            }

            return $value;
        }

        //去掉左右空格,防止字段前有多余空格
        $value = trim($value);

        // 使用AS的匿名
        if ($offset = strripos($value, ' AS '))
        {
            $alias = substr($value, $offset);
            $value = substr($value, 0, $offset);

            $alias = trim(str_ireplace(' AS ', '', $alias));
            //var_dump($alias, $value);    
            //return $this->quote_field($value) . ' AS ' . $this->quote_identifier($alias);
        }
        // 使用空格的匿名
        elseif ($offset = strrpos($value, ' '))
        {
            $alias = substr($value, $offset);
            $value = substr($value, 0, $offset);
            $alias = trim($alias);
            //return $this->quote_field($value) . ' AS ' . $this->quote_identifier($alias);
        }

        $parts = explode('.', $value);
        // 没有带表前缀
        if (count($parts) === 1) 
        {
            $table = $this->_table;
            $field = $parts[0];
        }
        else 
        {
            $table = $parts[0];
            $field = $parts[1];
        }
        $field = trim($field);

        $table = $this->table_prefix($table);
        $value = $this->quote_identifier($value);
  
        // 当前字段属于加密字段
        if ( 
            !empty(self::$config[$this->_db_name]['crypt_fields'][$table]) && 
            in_array($field, self::$config[$this->_db_name]['crypt_fields'][$table])
        ) 
        {
            // $value = "CONVERT(AES_DECRYPT({$value}, '".self::$config[$this->_db_name]['crypt_key']."') USING utf8)";
            $value = "AES_DECRYPT({$value}, '".self::$config[$this->_db_name]['crypt_key']."')";
            // 只处理SELECT子句中的字段
            if ($select && !$inside) 
            {
                // 为空直接用字段名
                if ( empty($alias) ) 
                {
                    $alias = $field;
                }
                $value = $value . " AS `{$alias}`";
            }
        }
        else 
        {
            if ( !empty($alias)) 
            {
                $value = $value . " AS `{$alias}`";
            }
        }

        return $value;
    }
    
    // 字段值是否加密
    public function quote_value($fields)
    {
        $table = $this->_table;

        $value = $fields[0];
        $field = $fields[1];

        if (is_object($value))
        {
            // 暂未使用
            //if ($value instanceof db_query)
            //{
                //// Create a sub-query
                //return '('.$value->compile($this).')';
            //}
            if ($value instanceof db_expression)
            {
                // Use a raw expression
                return $value->value();
            }
            else
            {
                // Convert the object to a string
                return $this->quote_identifier((string) $value);
            }
        }

        // 当前字段属于加密字段
        if ( 
            !empty(self::$config[$this->_db_name]['crypt_fields'][$table]) && 
            in_array($field, self::$config[$this->_db_name]['crypt_fields'][$table])
        ) 
        {
            $value = "AES_ENCRYPT('{$value}', '".self::$config[$this->_db_name]['crypt_key']."')";
        }
        //json字段
        else if( 
            !empty(self::$config[$this->_db_name]['json_fields'][$table]) && 
            in_array($field, self::$config[$this->_db_name]['json_fields'][$table])
        )
        {
            //更新
            if ( $this->_type == db::UPDATE && is_array($value) ) 
            {
                //清空
                if ( !$value ) 
                {
                    $value = '\'' . json_encode(new \stdClass) . '\'';
                }
                else
                {
                    $tmp = [$field];
                    foreach($value as $f => $ff)
                    {
                        //转成object是因为枚举数组没法更新
                        $ff    = is_array($ff) ? addslashes(json_encode((object)$ff, JSON_UNESCAPED_UNICODE)) : $ff;
                        //string的才加‘’,否则不加
                        // $ff    = is_string($ff) || !$ff ? "'{$ff}'" : $ff;
                        $tmp[] = "'$.\"{$f}\"', " . $this->_get_chr_value($ff);
                    }

                    $value = 'JSON_SET('.implode(", ", $tmp).')';
                }
            }
            //插入
            else
            {
                $value = !is_array($value) ?  $value : 
                    ($value ? json_encode($value, JSON_UNESCAPED_UNICODE) : json_encode(new \stdClass));
                $value = $this->quote($value);
            }
        }
        else 
        {
            $value =  is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            $value  = isset($value) ? $this->quote($value) : 'NULL';
        }

        return $value;
    }

    /**
     * Quotes an identifier so it is ready to use in a query.
     *
     * @param   string  $string the string to quote
     * @return  string  the quoted identifier
     */
    public function quote_identifier($value)
    {
        if ($value === '*')
        {
            return $value;
        }
        elseif (is_object($value))
        {
            // 暂未使用
            //if ($value instanceof db_query)
            //{
                //// Create a sub-query
                //return '('.$value->compile($this).')';
            //}
            if ($value instanceof db_expression)
            {
                // Use a raw expression
                return $value->value();
            }
            else
            {
                // Convert the object to a string
                return $this->quote_identifier((string) $value);
            }
        }
        elseif (is_array($value))
        {
            // Separate the column and alias
            list($value, $alias) = $value;

            return $this->quote_identifier($value).' AS '.$this->quote_identifier($alias);
        }

        // 如果传进来的SQL片段带有转义字符，直接返回，不进行下面的转义
        if (preg_match('/^(["\']).*\1$/m', $value))
        {
            return $value;
        }

        // 使用sum、max、min函数的处理
        if (strcspn($value, "()'") !== strlen($value))
        {
            if (preg_match("#\(([ \t\w\`]+)\)#i", $value, $matchs))
            {
                $match_value = $matchs[1];
                $quote_value = $this->quote_identifier($match_value);
                $value = str_replace("(".$match_value.")", "(".$quote_value.")", $value);
            }
            return $value;
        }

        // 去掉多余的空格
        $value = preg_replace('/\s+/', ' ', trim($value));
        $value = str_replace('`', '', $value);

        // 使用AS的匿名
        if ($offset = strripos($value, ' AS '))
        {
            $alias = substr($value, $offset);
            $value = substr($value, 0, $offset);
            return $this->quote_identifier([$value, trim(str_ireplace(' AS ', '', $alias))]);
            //$alias = " AS ".$this->quote_identifier( trim(str_ireplace("AS ", "", $alias)) );
        }
        // 使用空格的匿名
        elseif ($offset = strrpos($value, ' '))
        {
            $alias = substr($value, $offset);
            $value = substr($value, 0, $offset);
            return $this->quote_identifier([$value, trim($alias)]);
            //$alias = " ".$this->quote_identifier( $alias );
        }

        // 如果字段带表名
        if (strpos($value, '.') !== false)
        {
            $parts = explode('.', $value);

            if ($this->table_prefix())
            {
                // Get the offset of the table name, 2nd-to-last part
                // This works for databases that can have 3 identifiers (Postgre)
                $offset = count($parts) - 2;

                // Add the table prefix to the table name
                $parts[$offset] = $this->table_prefix($parts[$offset]);
            }

            // Quote each of the parts
            return implode('.', array_map(array($this, __FUNCTION__), $parts));
        }

        return "`{$value}`";
    }
    
    /**
     * Escape query for sql
     *
     * @param   mixed   $value  value of string castable
     * @return  string  escaped sql string
     */
    public function escape($value)
    {
        // SQL standard is to use single-quotes for all values
        return isset($value) ? "'$value'" : 'null';
    }

    /**
     * Start returning results after "OFFSET ..."
     *
     * @param   integer  $number  starting result number
     *
     * @return  $this
     */
    public function offset($number)
    {
        $this->_offset = (int) $number;

        return $this;
    }

    /**
     * Returns results as associative arrays
     *
     * @return  $this
     */
    public function as_assoc()
    {
        $this->_as_object = false;

        return $this;
    }

    /**
     * Returns results as objects
     *
     * @param   mixed $class classname or true for stdClass
     *
     * @return  $this
     */
    public function as_object($class = true)
    {
        $this->_as_object = $class;

        return $this;
    }

    public function as_sql()
    {
        $this->_as_sql = true;

        return $this;
    }

    /**
     * Returns results as objects
     *
     * @param   mixed $class classname or true for stdClass
     *
     * @return  $this
     */
    public function as_result($class = true)
    {
        $this->_as_result = $class;

        return $this;
    }

    public function as_row()
    {
        $this->_as_row = true;

        return $this;
    }

    public function as_field()
    {
        $this->_as_field = true;

        return $this;
    }

    public function get_type($sql) 
    {
        $type = 0;
        $stmt = preg_split('/[\s]+/', ltrim(substr($sql, 0, 11), '('), 2);
        switch(strtoupper(reset($stmt)))
        {
            case 'DESCRIBE':
            case 'EXECUTE':
            case 'EXPLAIN':
            case 'SELECT':
            case 'SHOW':
                $type = db::SELECT;
                break;
            case 'INSERT':
            case 'REPLACE':
                $type = db::INSERT;
                break;
            case 'UPDATE':
                $type = db::UPDATE;
                break;
            case 'DELETE':
                $type = db::DELETE;
                break;
            default:
                $type = 0;
        }

        return $type;
    }

    /**
     * Reset the query parameters
     * @return $this
     */
    public function reset()
    {
        $this->_sql        = null;
        $this->_type       = null;
        $this->_table      = null;
        $this->_select     = array();
        $this->_from       = array();
        $this->_join       = array();
        $this->_on         = array();
        $this->_where      = array();
        $this->_group_by   = array();
        $this->_having     = array();
        $this->_order_by   = array();
        $this->_distinct   = false;
        $this->_limit      = null;
        $this->_offset     = null;
        // insert
        $this->_columns    = array();
        $this->_values     = array();
        // update
        $this->_set        = array();
        $this->_atts       = array();

        $this->_as_sql     = false;
        $this->_as_object  = false;
        $this->_as_row     = false;
        $this->_as_field   = false;
        $this->_as_result  = false;

        $this->_parameters = array();

        $this->_max_select_limit = 300;

        return $this;
    }

    public function autocommit($mode = true, $retry = false)
    {
        try 
        {
            $name   = self::get_instance_name($this->_db_name, 'master');
            $handle = static::$_instance[$name]->_handler();
            //防止开启事物失败
            if ( ! @mysqli_ping($handle) ) 
            {
                $this->reconnect($name);
            }

            static::log_query('autocommit ' . ($mode ? 'true' : 'false'), 'sql', $name);
            return mysqli_autocommit(
                static::$_instance[$name]->_handler(), 
                $mode
            );
        } 
        catch (Exception $e) 
        {
            log::error(sprintf(
                "%s:%s [%s]", 
                $e->getCode(), 
                $e->getMessage(), 
                'autocommit'.'('.$name.')'), 
                'SQL Reconnect Error'
            );

            if ( $mode == false && !$retry ) 
            {
                $this->reconnect($name);
                return $this->autocommit($mode, true);
            }
            // 没有设置忽略错误
            else if ( empty($this->_atts['ignore']) ) 
            {
                throw new Exception($e->getMessage());
            }
        }

    }

    public function start()
    {
        static::log_query('autocommit false');
        $this->_atts['start'] = true; // 数据库重连可能会导致事务丢失，标记开启了事务不重连
        return $this->autocommit(false);
    }

    public function commit()
    {
        static::log_query('commit');
        return mysqli_commit(static::$_instance[self::get_instance_name($this->_db_name, 'master')]->_handler());
    }

    public function rollback()
    {
        static::log_query('rollback');
        return mysqli_rollback(static::$_instance[self::get_instance_name($this->_db_name, 'master')]->_handler());
    }

    public function end()
    {
        static::log_query('autocommit true');
        return $this->autocommit(true);
    }

    public function get_exception_trace($e) 
    {
        $ret   = [];
        $ret[] = $e->getMessage();
        $ret[] = sprintf('出错语句：%s', $sql ?? $this->_sql);
        $ret[] = $e->getTraceAsString();
        return implode(PHP_EOL, $ret);
    }    

    /**
    * SQL语句过滤程序（检查到有不安全的语句仅作替换和记录攻击日志而不中断）
    * @parem string $sql 要过滤的SQL语句 
    */
    public function filter_sql($sql)
    {
        $clean   = $error = '';
        $old_pos = 0;
        $pos     = -1;
        // 完整的SQL检查，当数据量超过 1万 条的时候会出现性能瓶颈，特别是 10万 条的时候特别慢，最好就处理 1万 条
        while (true)
        {
            $pos = strpos($sql, '\'', $pos + 1);
            if ($pos === false)
            {
                break;
            }
            $clean .= substr($sql, $old_pos, $pos - $old_pos);
            while (true)
            {
                $pos1 = strpos($sql, '\'', $pos + 1);
                $pos2 = strpos($sql, '\\', $pos + 1);
                if ($pos1 === false)
                {
                    break;
                }
                elseif ($pos2 == false || $pos2 > $pos1)
                {
                    $pos = $pos1;
                    break;
                }
                $pos = $pos2 + 1;
            }
            $clean .= '$s$';
            $old_pos = $pos + 1;
        }
        $clean .= substr($sql, $old_pos);
        $clean = trim(strtolower(preg_replace(array('~\s+~s'), array(' '), $clean)));
        $fail = false;
        // sql语句中出现注解
        if (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false)
        {
            $fail = true;
            $error = 'commet detect';
        }
        // 常用的程序里也不使用union，但是一些黑客使用它，所以检查它
        else if (strpos($clean, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = 'union detect';
        }
        // 这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
        elseif (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = 'slown down detect';
        }
        elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "slown down detect";
        }
        elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "file fun detect";
        }
        elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0)
        {
            $fail = true;
            $error = "file fun detect";
        }
        // 检测到有错误后记录日志并对非法关键字进行替换
        if ($fail === true)
        {
            $sql = str_ireplace(self::$rps, self::$rpt, $sql);

            // 进行日志
            $gurl = htmlspecialchars( req::cururl() );
            $msg = "{$gurl}\n".htmlspecialchars( $sql )."\n";
            log::warning($msg, 'filter_sql: ' . $error);
        }

        return $sql;
    }

    /**
     * 获取实例名称数组
     * @param string $name 实例名称
     * @param string $type master or slave
     */
    public static function get_instance_name($name = null, $type = null)
    {
        $name = self::get_muti_name($name);
        $instance_name = [
            'master' => $name .'_w',
            'slave'  => $name .'_r'
        ];

        return !empty($type) ? ($instance_name[$type] ?? $instance_name['master']) : $instance_name;
    }

    /**
     * cli模式加上进程ID,防止多进程实例串行
     * @param  string $name 实例名称
     * @return string       cli下带进程号的实例名称
     */
    public static function get_muti_name($name = null)
    {
        $muti_name = !$name ? self::$_default_name : $name;
        if (PHP_SAPI == 'cli')
        {
            $pid = ':' . posix_getpid();
            $len = strlen($pid);
            if ( substr($muti_name, -$len) != $pid ) 
            {
                $muti_name .= $pid;
                //兼容子进程下再开子进程
                if ( !isset(self::$config[$muti_name]) && isset(self::$config[$name]) ) 
                {

                    self::init_db($muti_name, self::$config[$name]['config_file']);
                }
            }
        }
        
        return $muti_name;
    }

    /**
     * 记录query的执行结果
     * @param  string       记录内容
     * @param  string       记录类型 sql、query_time
     * @return bool         返回bool
     */
    public static function log_query($content, $type = 'sql')
    {
        // 非cli、调试模式、本机记录 SQL 和 时间，方便调试
        if ( 
            PHP_SAPI != 'cli' || 
            (defined('SYS_DEBUG') && SYS_DEBUG === true) || 
            (defined('IP') && IP === '127.0.0.1') 
        )
        {
            if ($type == 'sql') 
            {
                db::$queries[] = $content;
            }
            elseif ($type == 'prepare_sql') 
            {
                db::$prepare_queries[] = $content;
            }
            else
            {
                db::$query_times[] = $content;
            }
        }

        return true;
    }

    /**
     * 返回数据库实例名
     *
     *     echo (string) $db;
     *
     * @return  string
     */
    final public function __toString()
    {
        return $this->_name;
    }

}
