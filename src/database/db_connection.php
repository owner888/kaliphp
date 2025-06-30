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

use kaliphp\req;
use kaliphp\util;
use kaliphp\log;
use kaliphp\db;
use kaliphp\event;
use kaliphp\config;
use Exception;

defined('IS_CLI') || define('IS_CLI', PHP_SAPI === 'cli');
/**
 * 数据库类
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
    protected $_columns = [];

    /**
     * @var array  $_values insert  values
     */
    protected $_values = [];

    /**
     * @var array  $_set  insert or update values
     */
    protected $_set = [];

    /**
     * @var array  $_select  columns to select
     */
    protected $_select = [];

    /**
     * @var bool  $_distinct  whether to select distinct values
     */
    protected $_distinct = false;

    /**
     * @var array  $_from  table name
     */
    protected $_from = [];

    /**
     * @var array  $_where  where statements
     */
    protected $_where = [];

    /**
     * @var array  $_having  having clauses
     */
    protected $_having = [];

    /**
     * @var  array  Quoted query parameters
     */
    protected $_parameters = [];

    /**
     * @var array  $_join  join objects
     */
    protected $_join = [];

    /**
     * @var array  $_on  ON clauses
     */
    protected $_on = [];

    /**
     * @var array  $_group_by  group by clauses
     */
    protected $_group_by = [];

    /**
     * @var array  $_order_by  order by clause
     */
    protected $_order_by = [];

    /**
     * 遇到相同主键/unique key更新，否则插入
     * @var array
     * array
     */
    protected $_dups = [];

    /**
     * 加密字段
     * @var array
     */
    protected $_crypt_fields = [];

    /**
     * @var  integer  $_limit
     */
    protected $_limit = null;

    /**
     * @var  integer  $_max_select_limit
     */
    protected $_max_select_limit = 0;
    
    /**
     * @var integer  $_offset  offset
     */
    protected $_offset = null;

    /**
     * clickhouse中类似窗口函数的支持
     * @var null
     */
    protected $_limit_by = null;

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
     * @var  resource  $_result raw result resource
     */
    private $_result;

    public static $config = [];

    public static $global_configs = [];

    //当前实例名称，方便多库使用的时候自定义实例名称
    private static $_instance_name = [];

    //默认数据库名称
    private static $_default_name  = null;

    private static $_orgin_default = 'default';


    public static $rps = [
        '/*', '--', 'union', 'sleep', 'benchmark', 'load_file', 'outfile',
        'extractvalue', 'updatexml', 'information_schema', 'performance_schema'
    ];
    public static $rpt = [
        '/×', '——', 'ｕｎｉｏｎ', 'ｓｌｅｅｐ', 'ｂｅｎｃｈｍａｒｋ', 'ｌｏａｄ_ｆｉｌｅ', 'ｏｕｔｆｉｌｅ',
        'ｅｘｔｒａｃｔｖａｌｕｅ', 'ｕｐｄａｔｅｘｍｌ', 'ｉｎｆｏｒｍａｔｉｏｎ＿ｓｃｈｅｍａ', 'ｐｅｒｆｏｒｍａｎｃｅ＿ｓｃｈｅｍａ'
    ];

    private static $chr2 = null;
    private static $chr3 = null;

    /**
     * 初始化
     */
    public static function _init()
    {
        static::$chr2           = chr(2);
        static::$chr3           = chr(3);
        static::$_default_name  = self::$_orgin_default;
        static::$global_configs = config::instance('database')->get('global_db_configs') ?: [];
        return static::init_db();
    }

    /**
     * 初始化数据库
     * @param string $name 实例名称
     * @param string $name 数据库配置文件名
     * @param bool $default_instance 是否设为默认数据库
     */
    public static function init_db(
        ?string $name             = null, 
        ?string $config_file      = null, 
        ?bool   $default_instance = false, 
        ?array  $slave_index      = null
    )
    {
        list($orgin_name, ) = $name ? explode(':', $name) : null;
        $name               = $name ?? self::$_orgin_default;
        $muti_name          = self::get_muti_name($name);
        $_config_key        = null;
		// 引入其他模块的类配置文件(支持直接指定的database.php的某一个key)
        if ( $config_file && strstr($config_file, ':') ) 
        {
            list($_config_file, $_config_key) = explode(':', $config_file);
        }
		// 加载其他配置文件
        else if ( $config_file )
        {
            $_config_file = $config_file;
        }
		// 默认配置文件
        else
        {
            $_config_file = 'database'; // 加载的数据库配置文件名，可以配置成其他文件
        }

        // 兼容 name 当 key 到 database 中取的方式
        if ( !$config_file && $orgin_name ) 
        {
            self::$config[$name] = config::instance($_config_file)->get($orgin_name);
			// 取不当尝试取为 name 的配置
            if ( !self::$config[$name] && $name != self::$_default_name ) 
            {
                try 
                {
                    $configs = config::instance($orgin_name)->get();
                    self::$config[$name] = $configs;
                } 
                catch (Exception) 
                {
                }
            }
        }

        if ( empty(self::$config[$name]) ) 
        {
            self::$config[$name] = config::instance($_config_file)->get($_config_key);
        }
        
        if ( self::$config[$name] ) 
        {
            self::$config[$name] = array_merge(self::$config[$name], [
                'config_file' => $config_file
            ]);
        }

        if ( !self::$config[$name] )
        {
            throw new Exception("Load {$config_file} fail", 3001);
        }
        else if ( isset(self::$config[$name]['host']['master']) )
        {
            $instance_name = self::get_instance_name($muti_name);
			// 第一个为默认数据库
            if ( !self::$_instance_name && $default_instance )
            {
                self::$_default_name  = $muti_name;
            }

			// 如果没有初始化
            if ( !isset(self::$_instance[$instance_name['master']]) )
            {
                // 链接主库
                list($host, $port) = explode(":", self::$config[$name]['host']['master']);
                $config = [
                    'host'             => $host,
                    'port'             => $port,
                    'user'             => self::$config[$name]['user'] ?? null,
                    'pass'             => self::$config[$name]['pass'] ?? null,
                    'name'             => self::$config[$name]['name'],
                    'timeout'          => self::$config[$name]['timeout'],
                    'read_timeout'     => self::$config[$name]['read_timeout'] ?? null,
                    'charset'          => self::$config[$name]['charset'],
                    'gcm_len'          => self::$config[$name]['group_concat_max_len'] ?? null,
                    'disabled_prepare' => self::$config[$name]['disabled_prepare'] ?? null,
                ];
                
                $config = self::_get_config($host, $port, $config);
                self::instance($instance_name['master'], $name, $config);
				// var_dump(self::instance());
				// exit;

                // 如果配置了从库 链接从库
                if ( !empty(self::$config[$name]['host']['slave']) )
                {
                    $slaves   = self::$config[$name]['host']['slave'];
                    $slave_fn = self::$config[$name]['host']['slave_fn'] ?? null;
                    if ( $slave_fn && $slave_fn instanceof \Closure ) 
                    {
                        $slaves = $slave_fn($config, $slaves);
                    }

                    if ( $slave_index ) 
                    {
                        foreach (array_keys($slaves) as $k)
                        {
                            if ( !in_array($k, $slave_index) ) 
                            {
                                unset($slaves[$k]);
                            }
                        }

                        $slaves = array_values($slaves);
                    }

                    if ( !$slaves ) 
                    {
                        goto MASTER_DB;
                    }
                    else
                    {
                        $slave = $slaves[mt_rand(0, count($slaves) - 1)];
                    }

                    // 从库和主库账号密码不一致的时候，置换账号密码
                    if ( 
                        !empty(self::$config[$name]['slave_users']) &&
                        isset(self::$config[$name]['slave_users'][$slave])
                    ) 
                    {
                        [$config['user'], $config['pass']] = self::$config[$name]['slave_users'][$slave];
                    }

                    list($r_host, $r_port) = explode(":", $slave);
                    // 从库和主库一样，使用主库连接
                    if ( $r_host == $host && $r_port == $port ) 
                    {
                        goto MASTER_DB;
                    }
                    else
                    {
                        $config = self::_get_config($r_host, $r_port, $config);
                        $config = array_merge($config, array('host' => $r_host, 'port' => $r_port));           
                        self::instance($instance_name['slave'], $name, $config);
                    }
                }
                //否则从库使用主库的链接
                else
                {
                    MASTER_DB:
                    static::$_instance[$instance_name['slave']] = &static::$_instance[$instance_name['master']];
                }
            }

            return $name;
        }

        return false;
    }

    /**
     * 合并公共配置
     * @param string  $host
     * @param integer $port
     * @param array $config
     * @return array
     */
    private static function _get_config(string $host, int $port, array $config):array
    {
        $new_config = static::$global_configs["{$host}:{$port}"] ?? null;
        if ( $new_config && is_array($new_config) ) 
        {
            foreach ($new_config as $k => $v)
            {
                $config[$k] = $v;
            }
        }

        return $config;
    }

    /**
     * 获取db的名称，兼容指定slave
     * @param  string|null $name
     * @param  array|null $slave_index
     * @return string
     */
    public static function get_db_name(?string $name = null, ?array $slave_index = null)
    {
        return ($name ?? self::$_default_name) . ($slave_index ? ':' . implode('#', $slave_index) : '');
    }

    /**
     * 实例话数据库，返回当前主库对象
     * @param  string|null $name
     * @param  string|null $config_file
     * @param  boolean|null $default_instance
     * @param  array|null $slave_index
     * @return object
     */
    public static function new_db(
        ?string $name             = null, 
        ?string $config_file      = null, 
        ?bool   $default_instance = false, 
        ?array  $slave_index      = null
    )
    {
        $name          = self::get_db_name($name, $slave_index);
        $instance_name = static::get_instance_name($name, 'master');
        if ( 
            isset(static::$_instance[$instance_name]) || 
            static::init_db($name, $config_file, $default_instance, $slave_index) 
        )
        {
            return static::$_instance[$instance_name];
        }
        else
        {
            throw new Exception(sprintf('%s[%s] error', __function__, $instance_name), 3001);
        }
    }

    /**
     * 获取当前配置信息
     * @DateTime 2022-08-16
     * @param    string|null $name
     * @param    string|null $key 
     * @return   mixed        
     */
    public static function get_config(?string $name = null, ?string $key = null)
    {
        $name = $name ?? self::$_default_name;
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

            if (isset($this->_config['keep-alive']) && $this->_config['keep-alive'])
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
                if ( !IS_CLI && !empty($this->_config['read_timeout']) ) 
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
                defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE') &&
                $this->_handler->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
                mysqli_query($this->_handler, "SET NAMES ".$this->_config['charset']);
                if ( !empty($this->_config['gcm_len']) ) 
                {
                    mysqli_query(
                        $this->_handler, 
                        'SET SESSION group_concat_max_len='.$this->_config['gcm_len']
                    );
                }

                //标记最近的一次sql所使用的实例名称
                self::$config[$this->_db_name]['current_instance'] = $this->_name;
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
        foreach ($instance_name as $k => $v)
        {
            if ( isset(self::$_instance[$v]) )
            {
                self::$_instance_name[$k] = $v;
                self::$_default_name      = $name;
                $result = true;
            }
        }

        return $result;
    }

    /**
     * 单例
     * @param string $name
     * @param bool $instance
     * @return object
     */
    public static function instance($name = null, $db_name = null, ?array $config = null, bool $force_init = false)
    {
        if ($name === null)
        {
            // Use the default instance name
            $name = !empty(self::$_instance_name['master']) ? 
                self::$_instance_name['master'] : 
                self::get_instance_name(self::$_default_name, 'master');
        }

        if ( $force_init || !isset(static::$_instance[$name]))
        {
            $db_name = $db_name ?: self::$_default_name;
            static::$_instance[$name] = new static($name, $db_name, $config);
            // 非cli下，cgi结束后主动关闭链接
            !IS_CLI && util::shutdown_function(function() use($name) {
                static::$_instance[$name]->close($name);
            });
        }

        return static::$_instance[$name];
    }

    public function close($instance = null)
    {
        foreach (static::$_instance as $name => $db_instance)
        {
            if ( (!$instance || $instance == $name) ) 
            {
                if ( is_object($db_instance->_handler) ) 
                {
                    @mysqli_close($db_instance->_handler);
                }
                
                static::$_instance[$name]->_handler = null;
            }
        }
       
        return false; 
    }

    public function reconnect(?string $name = null)
    {
        $name = $name ?? $this->_name;
        self::close($name);
        self::instance($name, $this->_db_name, $this->_config, true);
    }

    public function __construct($name, $db_name, $config)
    {
        // 设置实例名
        $this->_name    = $name;
        $this->_db_name = $db_name;
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

    /**
     * 执行sql
     * @param mixed   $sql 如果为数组的时候，必须包含0和1，0表示sql模版，1为数组，表示模版里面的变量
     * $this->query([
     *   'select * from xxxx where xxx1 in {xxx1} and xxx2={xxx2}',
     *    [
     *        'xxx1' => 1,
     *        'xxx2' => [2, 3, 'a'],
     *    ]
     *]);
     * @param boolean $close_filter
     * @return $this
     */
    public function query($sql, bool $close_filter = false)
    {
        // 参数化
        if ( is_array($sql) ) 
        {
            $has_array = false;
            $sql       = preg_replace_callback(
                '#{\s*(\w+)\s*\}#isU', 
                function($mat) use($sql, &$has_array) {
                    $val = $sql[1][$mat[1]] ?? null;
                    !$has_array && $has_array = is_array($val);
                    return $this->quote($val);
                }, 
                $sql[0]
            );

            // 替换重复的(())
            if ( $has_array ) 
            {
                $sql = preg_replace('#in\s+\(\s*(\(.*\))\s*\)#isU', 'IN \\1', $sql);
            }
        }

        // Change #PB# to db_prefix
        $sql = $this->table_prefix($sql);
        if ( !$close_filter && self::$config[$this->_db_name]['safe_test'] )
        {
            $sql = $this->filter_sql($sql);
        }

        $this->_sql = $sql;
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

            if ( !empty($this->_atts['lock']) )
            {
                $this->_atts['lock'] = false;//用过一次后释放
                $this->_sql .= " FOR UPDATE";
            }
            else if ( !empty($this->_atts['share']) )
            {
                $this->_atts['share'] = false;//用过一次后释放
                $this->_sql .= " LOCK IN SHARE MODE";
            }
        }

        // 兼容字段中有复杂计算不替换#PB#的情况
        $this->_sql = $this->table_prefix($this->_sql);
        return $this->_sql;
    }

    /**
     * Execute the current query on the given database.
     *
     * @param   mixed   $is_master Database master or slave
     * @param   array   $params index
     * @param   mixed   $sql 如果传了，就直接执行这个sql，用于Mysql等待超时重新执行使用
     *
     * @return  mixed   SELECT queries
     */
    public function execute($is_master = false, $params = [], $sql = null)
    {
        // Compile the SQL query
        $sql        = $sql ? $sql : $this->compile();
        $real_sql   = $this->convert_back_sql($sql);
        $this->_sql = $real_sql;

        // 获取当前实例组
        $instance_group = $this->_atts['instance_name'] ?? $this->get_instance_group();
        // 用户手动指定使用主数据库 或 从数据库状态不可用
        if ( 
            $is_master === true || 
            (isset($is_master) && $this->_enable_slave === false) || 
            !empty($this->_atts['lock'])
        )
        {
            $db_name = $instance_group['master'];
        }
        else
        {
            if ($this->_type === db::SELECT)
            {
                $db_name = $instance_group['slave'];
            }
            else
            {
                $db_name = $instance_group['master'];
            }
        }

        // echo $db_name;echo "{$sql}<br>";
        $db_conn = self::$_instance[$db_name];
        db::$query_db_names[] = $db_name;
        try
        {
            // Start the Query Timer
            $time_start = microtime(true);
            // 加 @ 去掉下面两个警告
            // mysqli_query(): MySQL server has gone away
            // mysqli_query(): Error reading result set's header
            // $this->_result = @mysqli_query($db_conn->_handler(), $sql);
            if ( !empty($db_conn->_config['disabled_prepare']) ) 
            {
                $this->_result = @mysqli_query($db_conn->_handler(), $this->convert_back_sql($sql));
            }
            else 
            {
                $stmt = $this->convert_prepare_sql($db_conn, $sql);
            }

            // Stop and aggregate the query time results
            $query_time = microtime(true) - $time_start;
            $this->log_query($query_time, 'query_time');
            $this->log_query($real_sql, 'sql', $db_name . ':' . $db_conn->_config['host'] , round($query_time, 6));
            // 触发SQL事件
            //event::trigger(onSql, [$real_sql, $db_name, round($query_time, 6)]);

            // 记录慢查询
            if ( 
                ( !defined('SYS_DEBUG') || SYS_DEBUG )  &&
                self::$config[$this->_db_name]['slow_query'] && 
                ($query_time > self::$config[$this->_db_name]['slow_query']) 
            )
            {
                event::trigger(onWarn, [$db_name, $real_sql, round($query_time, 6)]);
                // log::warning(sprintf('Slow Query [%s]: %s (%ss)', $db_name, $real_sql, round($query_time, 6)));
            }

            $result = null;
            if ($this->_type === db::INSERT)
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
                // log::write('test_sql', $db_conn);
                // log::write('test_sql', self::$_instance);
                // Return the number of rows affected
                $result = mysqli_affected_rows($db_conn->_handler());
                self::log_query($result, $this->_type === db::UPDATE ? 'update' : 'delete');
            }
            else
            {
                isset($stmt) && $this->_result = $stmt->get_result();
                if ( $this->_as_result ) 
                {
                    $result = $this->_result;
                }
                else if ( $this->_result )
                {
                    $rows = [];
                    while ($row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC))
                    {
                        if ( !empty($this->_atts['row_fn']) )
                        {
                            call_user_func_array($this->_atts['row_fn'], [$row, &$rows]);
                        }
                        else if ( empty($params['index']) ) 
                        {
                            $rows[] = $row;
                        }
                        else 
                        {
                            $rows[$row[$params['index']]] = $row;
                        }
                    }

                    if ( $this->_as_field ) 
                    {
                        $result = reset($rows) ?: NULL;
                        $result && $result = reset($result) ?: NULL;
                    }
                    elseif ( $this->_as_row ) 
                    {
                        $result = reset($rows) ?: [];
                    }
                    else 
                    {
                        $result = $rows ?: [];
                    }

                    mysqli_free_result($this->_result);
                }
            }
        }
        catch (Exception $e)
        {
            $errno   = $e->getCode();
            $err_msg = sprintf(
                "%s:%s [%s (%s=>%s:%s:%s)]", 
                $errno, 
                $e->getMessage(), 
                $real_sql, 
                $db_conn->_name,
                $db_conn->_config['name'] ?? null, 
                $db_conn->_config['host'] ?? null,
                $db_conn->_config['port'] ?? null
            );
            
            $this->_atts['reconnect_times'] = isset($this->_atts['reconnect_times']) ? ++$this->_atts['reconnect_times'] : 1;
            // Mysql 等待超时,如果是开启了事务，不应该重试，因为重连可能导致事务id发生变化
            if ( 
                empty($this->_atts['start']) && in_array($errno, [2013, 2006, 4031]) &&
                //每个查询超出最大重连次数，不再重连，防止触发max_connect_errors，无法连接数据库
                $this->_atts['reconnect_times'] <= $this->_max_reconnect
            ) 
            {
                log::info($err_msg, 'SQL Reconnect');
                // 重新链接，$this 默认是default_w 的
                //$this->reconnect();
                $db_conn->reconnect($db_conn->_name);
                // 再次执行当前方法
                return $this->execute($is_master, $params, $sql);
            }
            // 死锁重试
            else if ( 
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

                log::info($err_msg, 'Deadlock Retry');
                return $this->execute($is_master, $params, $sql);
            }

            log::error($errno . '=>' . $err_msg, 'SQL Error');
            $ignore = $this->_atts['ignore'] ?? false;
            // 没有设置忽略错误
            if ( !$ignore ) 
            {
                //$this->_sql = $sql;
                $tracemsg   = $this->get_exception_trace($e, $real_sql);
                throw new Exception($tracemsg);
            }

            return null;
        }
        finally 
        {
            if ( isset($stmt) && $stmt ) 
            {
                $stmt->close();
            }

            $this->reset();
        }

        return $result;
    }

    public function link_id()
    {
        return mysqli_thread_id($this->_handler());
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
        // 子查询语句
        else if ( is_object($tables) )
        {
            $this->_from[0] = $tables;
            return $this;
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
            if (func_num_args() === 2)
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
            if (func_num_args() === 2)
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
    public function limit(int $number)
    {
        if ($number != 0) 
        {
            $this->_limit = $number;
        }
        return $this;
    }

    /**
     * Return up to "BY..." results
     *
     * @param   integer  $number  maximum results to return
     *
     * @return  $this
     */
    public function limit_by($field)
    {
        $this->_limit_by = $field;
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
        if ( $type )
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
        foreach ($columns as $idx => $column)
        {
            // if an array of columns is passed, flatten it
            if ( is_array($column) )
            {
                foreach ($column as $c)
                {
                    if ( $c ) 
                    {
                        $columns[] = $c;
                    }
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
        if (func_num_args() === 2)
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
        if (func_num_args() === 2)
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

    public function get_compiled_sql()
    {
        // Compile the SQL query
        $sql = $this->compile();
        $this->reset();
        return $sql;
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
            foreach ($this->_atts['union'] as $v)
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
        else if ( !empty($this->_from) )
        {
            // Set tables to select from
            $sql .= ' FROM '.implode(', ', array_unique(array_map($quote_table, $this->_from)));
        }

        if ( !empty($this->_atts['index_name']) )
        {
            $sql .= ' FORCE INDEX('.$this->_atts['index_name'].')';
            $this->_atts['index_name'] = '';
        }

        if ( !empty($this->_join) )
        {
            // Add tables to join[$table]
            $sql .= ' '.$this->_compile_join($this->_join);
        }

        if ( !empty($this->_where) )
        {
            // Add selection conditions
            $sql .= ' WHERE '.$this->_compile_conditions($this->_where);
        }

        if ( !empty($this->_group_by) )
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
            foreach ($this->_atts['union'] as $v)
            {
                $sql .= sprintf(' UNION %s (%s)', ($v['type'] ? ' ALL ' : ''), $v['sql']);
            }
        }

        if ( !empty($this->_order_by) )
        {
            // Add sorting
            $sql .= ' '.$this->_compile_order_by($this->_order_by);
        }

        if ( $this->_as_row || $this->_as_field ) 
        {
            $this->_limit = 1;   
        }

        // select 查询 limit 需要做下限制
        if ( PHP_SAPI !== 'cli' && $this->_max_select_limit ) 
        {
            $this->_limit = empty($this->_limit) ? $this->_max_select_limit : min($this->_limit, $this->_max_select_limit);
        }

        if ( $this->_limit !== NULL )
        {
            // Add limiting
            $sql .= ' LIMIT '.$this->_limit;
        }

        if ( $this->_limit_by !== NULL )
        {
            // Add limiting
            $sql .= ' BY '.$this->_limit_by;
        }

        if ( $this->_offset !== NULL )
        {
            // Add offsets
            $sql .= ' OFFSET '.$this->_offset;
        }

        if ( !empty($this->_atts['lock']) && empty($this->_as_row) ) 
        {
            $this->_atts['lock'] = false;//用过一次后释放
            $sql .= ' FOR UPDATE';
        }
        else if (  !empty($this->_atts['share']) && empty($this->_as_row) ) 
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

    public function get_current_db_name()
    {
        return $this->_atts['instance_name']['db_name'] ?? $this->_db_name;
    }

    public function get_compiled_insert()
    {
        $table  = $this->table_prefix($this->_table);
        // Start an insertion query
        $sql    = 'INSERT ' . $this->_get_compiled_atts() .' INTO '.$table;
        //因为json字段初始化不能为空，否则后面是没发更新的，必须给他一个默认值{}
        if ( !empty(self::$config[$this->get_current_db_name()]['json_fields'][$table]) ) 
        {
            foreach (self::$config[$this->get_current_db_name()]['json_fields'][$table] as $field) 
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
            // 批量插入的时候字段必须一致，所以以第一个数组的field为准
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
                $groups[] = '(' . implode(', ', array_map($quote, $group)) . ')';
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
            $sql .= ' ON DUPLICATE KEY  UPDATE ' . $this->_compile_dups($this->_dups);
            $this->_dups = [];
        }

        return $sql;
    }

    /**
     * 设置批量更新字段
     * @DateTime 2022-05-31
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
        $sql = 'UPDATE ' . $this->_get_compiled_atts() .
            $this->table_prefix($this->_table);
        if ( ! empty($this->_join))
        {
            // Add tables to join
            $sql .= ' ' . $this->_compile_join($this->_join);
        }

        //批量更新
        if ( isset($this->_atts['update_cmp_field']) ) 
        {
            $sql .= ' SET ' . $this->_compile_batch_update_set($this->_set);
        }
        else
        {
            // Add the columns to update
            $sql .= ' SET ' . $this->_compile_set($this->_set);
        }

        if ( ! empty($this->_where))
        {
            // Add selection conditions
            $sql .= ' WHERE ' . $this->_compile_conditions($this->_where);
        }

        if ( ! empty($this->_order_by))
        {
            // Add sorting
            $sql .= ' ' . $this->_compile_order_by($this->_order_by);
        }

        if ($this->_limit !== null)
        {
            // Add limiting
            $sql .= ' LIMIT ' . $this->_limit;
        }
        
        return $sql;
    }

    public function get_compiled_delete()
    {
        // Start a deletion query
        $sql = 'DELETE ' . $this->_get_compiled_atts(). ' FROM ' . $this->table_prefix($this->_table);
        if ( ! empty($this->_where))
        {
            // Add deletion conditions
            $sql .= ' WHERE ' . $this->_compile_conditions($this->_where);
        }

        if ( ! empty($this->_order_by))
        {
            // Add sorting
            $sql .= ' ' . $this->_compile_order_by($this->_order_by);
        }

        if ($this->_limit !== null)
        {
            // Add limiting
            $sql .= ' LIMIT ' . $this->_limit;
        }

        return $sql;
    }

    // 暂时没用
    // public function get_fields($table)
    // {
    //     // $sql = "SHOW COLUMNS FROM $table"; //和下面的语句效果一样
    //     $rows = db::get_all("Desc `{$table}`");
    //     $fields = array();
    //     foreach ($rows as $v)
    //     {
    //         // 过滤自增主键
    //         // if ($v['Key'] != 'PRI')
    //         if ($v['Extra'] != 'auto_increment')
    //         {
    //             $fields[] = $v['Field'];
    //         }
    //     }
    //     return $fields;
    // }

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
     * @param   mixed  $column  table name or [$table, $alias] or object
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
            $conditions = [];
            foreach ($this->_on[$key] as $condition)
            {
                // Split the condition
                list($c1, $op, $c2, $chaining) = $condition;

                // Add chain type
                $conditions[] = ' ' . $chaining . ' ';

                if ($op)
                {
                    // Make the operator uppercase and spaced
                    $op = ' ' . strtoupper($op);
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
                
                $conditions[] = $c1 . $op . ' ' . (is_null($c2) ? 'NULL' : $c2);
            }

            // remove the first chain type
            array_shift($conditions);

            // if there are conditions, concat the conditions "... AND ..." and glue them on...
            empty($conditions) or $joins[$key] .= ' ON (' . implode('', $conditions) . ')';

        }

        $sql = implode(' ', $joins);
        return $sql;
    }

    /**
     * 返回匹配边界符，方便prepare替换
     * @param  mixed $value 
     * @return string      
     */
    protected function get_chr_value($value)
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
    protected function get_chr_pattern($width_quote = false)
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
    public function convert_back_sql(string $sql)
    {
        return preg_replace_callback($this->get_chr_pattern(), function($matches) {
            return $matches[1];
        }, $sql);
    }

    /**
     * 返回 prepare stmt
     * @param  object $db_conn
     * @param  string $sql    
     * @return object 
     */
    protected function convert_prepare_sql($db_conn, $sql) 
    {
        $params = [];
        $types  = '';
        // 使用 preg_replace_callback 函数将值替换为 ? 占位符，并将值存储在数组中
        // bind_param的时候不能根据参数自动判断类型，因为数据库类型是varchar/char的时候这个时候给一个int,模版是i
        // 会导致索引失效，甚至导致返回错误数据，比如搜索123的时候，会把123xxxx查询处理，因为给他i,他会把数据库中的字段转成int
        // 索引模版直接全部给s,就不会出现因为类型错误，导致索引失效/数据错误的bug
        $prepared_sql = preg_replace_callback(
            $this->get_chr_pattern(true), 
            function($matches) use (&$params, &$types) {
                $val = $matches['where_val'];
                $params[] = $val;
                $types   .= 's';
                return '?';
            }, 
            $sql
        );

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
            // 不能使用这种方式，数据库是非int,给一个int,模版变成i,会引发数据库字段类型转换，导致索引失效/数据查询错误
            // $types = ''; // 参数类型字符串
            // // 根据参数类型设置参数类型字符串
            // foreach ($params as $param) 
            // {   
            //     if ( is_numeric($param) ) 
            //     {
            //         if (strpos($param, '.') !== false) 
            //         {
            //             $types .= 'd'; // 双精度浮点型
            //         } 
            //         else 
            //         {
            //             $types .= 'i'; // 整型
            //         }
            //     } 
            //     else if (is_string($param)) 
            //     {
            //         $types .= 's'; // 字符串类型
            //     } 
            //     else 
            //     {
            //         $types .= 's'; // 默认为字符串类型
            //         $param  = strval($param); // 将不支持的参数类型转换为字符串
            //     }
            // }
            // 使用 call_user_func_array 函数将参数绑定到 prepared statement
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        static::log_query($prepared_sql, 'prepare_sql');
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
    public function _compile_conditions(?array $conditions = null)
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
                        $sql .= ' ' . $logic . ' ';
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
                        $sql .= ' ' . $logic . ' ';
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
                            $sql .= $column . '->\'$.' . $json_field . '\' ' . $op . ' ' . $value;
                        }
                        else
                        {
                            // Append the statement to the query
                            $column = $this->quote_field($column, false);
                            if ($op === 'FIND_IN_SET') 
                            {
                                $sql .= $op . "( '{$value}', {$column} )";
                            }
                            else 
                            {
                                $sql .= $column . ' ' . $op . ' ' . $value;
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
            if ( is_string($value) AND array_key_exists($value, $this->_parameters) )
            {
                // Use the parameter value
                $value = $this->_parameters[$value];
            }

            $value = $this->quote_value([$value, $column]);
            $column = $this->quote_identifier($column);

            // Quote the column name
            $set[$column] = $column .' = ' . $value;
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
        foreach ($values as $group) 
        {
            // Split the dups
            list($column, $value) = $group;
            if (is_string($value) AND array_key_exists($value, $this->_parameters)) 
            {
                // Use the parameter value
                $value = $this->_parameters[$value];
            }

            // json字段
            if ( is_array($value) && false != $this->_check_json_field($column) )
            {
                if ( !$value ) 
                {
                    $value = '\'' . json_encode(new \stdClass) . '\'';
                }
                else
                {
                    $tmp = [$column];
                    foreach ($value as $f => $ff)
                    {
                        $ff    = is_array($ff) ? addslashes(json_encode((object)$ff, JSON_UNESCAPED_UNICODE)) : $ff;
                        //string的才加‘’,否则不加
                        // $ff    = is_string($ff) || !$ff ? "'{$ff}'" : $ff;
                        $tmp[] = "'$.{$f}', " . $this->quote($ff);
                    }

                    $value = 'JSON_SET(' . implode(", ", $tmp) . ')';
                }
            }
            // 兼容`xxx`和values(`xxx`)
            else if ( !preg_match('#values\s*\([^\)]+\)#i', $value) )
            {
                $value = $this->quote_value(array($value, $column));
            }

            // Quote the column name
            $column = $this->quote_identifier($column);
            $dups[$column] = $column . ' = ' . $value;
        }

        return implode(', ', $dups);
    }

    /**
     * 检查某个字段是否在json中
     * @param  string $column 
     * @return bool   true/false
     */
    private function _check_json_field(string $column) : bool
    {
        if (      
            !empty(self::$config[$this->get_current_db_name()]['json_fields'][$this->_table]) && 
            in_array($column, self::$config[$this->get_current_db_name()]['json_fields'][$this->_table])
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

            if ( $direction )
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
                substr($from, 0, 1) !== ':' and $from = ':' . $from;
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
        $this->_atts['ignore'] = (bool) $value;
        return $this;
    }

    /**
     * 禁止记录日志
     * @param    boolean    $value
     * @return   $this   
     */
    public function un_log($value = true)
    {
        $this->_atts['un_log'] = (bool) $value;
        return $this;
    }

    /**
     * 延时插入/更新/删除等操作,可以大大降低服务器压力，如果是强业务型的一般不能使用
     * @param  integer $value 
     * 1. LOW_PRIORITY会导致客户端程序一直等待，直到其他客户端程序完成任务，它才会尝试插入操作
     * 2. DELAYED，当释放客户端程序来执行其他语句的时候，要被插入的数据行会在一个缓冲区中排队等候
     * @return $this
     */
    public function delay($value = 1)
    {
        $this->_atts['delay'] = (int) $value;
        return $this;
    }

    // 强制使用索引，非主键索引，行数占比太多，优化器不会跑索引，而是全表扫描
    // 一些行级锁的操作，使用的是非主键的索引的必须带上，否则会死锁
    public function force_index($index_name)
    {
        if ( !empty($index_name) )
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
    public function from_db(
        ?string $name        = null, 
        ?string $config_file = null, 
        ?bool   $default_db  = null, 
        ?array  $slave_index = null
    )
    {
        // if ( $name )
        // {
            $name = self::get_db_name($name, $slave_index);
            self::init_db($name, $config_file, $default_db, $slave_index);
            $instance_name = self::get_instance_name($name);
            if ( !isset(self::$_instance[$instance_name['master']]) )
            {
                throw new Exception("instance:{$name} is not exit", 3001);
            }

            $this->_atts['instance_name'] = $instance_name;
            $this->_db_name = $name;
        // }

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
        foreach (self::$_instance as $instance)
        {
            if ( $instance->_where )
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
            if ( is_array($table) ) 
            {
                $table[0] = self::table_prefix($table[0]);
            }
            else if ( is_string($table) )
            {
                $table = str_replace('#PB#', self::$config[$this->_db_name]['prefix'], trim($table));
                $table = str_replace('#!PB#', '#PB#', $table);
            }
            
            return $table;
        }

        return self::$config[$this->_db_name]['prefix'];
    }

    public function errno() 
    {
        return mysqli_errno($this->_handler());
    }

    public function error() 
    {
        return mysqli_error($this->_handler());
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
        if ( !isset($value) )
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
                //$value = '('.$value->compile($this).')';
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
            return !$value ? "('')" : '(' . implode(', ', array_map(array($this, __function__), $value)) . ')';
        }
        // elseif (is_int($value))
        // {
        //     // $value = "'{$value}'";
        // }
        // elseif (is_float($value))
        // {
        //     // Convert to non-locale aware float to prevent possible commas
        //     //$value = sprintf('%F', $value);
        //     // $value = $value;
        // }
        // else
        // {
        //     // prepare 预处理不需要自己转义，否则会出现双重转义
        //     //$value = $this->escape($value);
        // }

        return $this->get_chr_value($value);
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
        if ( is_object($value) ) 
        {
            return (string) $value;
        }
        // Assign the table by reference from the value
        else if (is_array($value))
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

            return $value.' AS ' . $this->quote_identifier($alias);
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
                $match_value     = $matchs[1];
                $match_value_arr = explode(",", $match_value);
                $tmp_value_arr   = array();
                foreach ($match_value_arr as $v) 
                {
                    $v = trim(str_replace('`', '', $v));
                    $v = $this->quote_identifier($v);
                    $v = $this->quote_field($v);
                    $tmp_value_arr[] = $v;
                }

                $quote_value = implode(", ", $tmp_value_arr);
                $quote_value = preg_replace('#as\s+`[^`]+`#i', '', $quote_value);
                $value = str_ireplace("concat(" . $match_value . ")", "CONCAT(" . $quote_value . ")", $value);
            }
            // 匹配空格、tab符号、`符号
            elseif ( 
                !preg_match('#distinct\s+#i', $value) && 
                preg_match("#\(([ \t\w\`]+)\)#i", $value, $matchs)
            )
            {
                $match_value = $matchs[1];
                $quote_value = $this->quote_field($match_value, $select, true);
                $value = str_replace("(" . $match_value . ")", "(" . $quote_value . ")", $value);
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
            $value = "AES_DECRYPT({$value}, '" . self::$config[$this->_db_name]['crypt_key'] . "')";
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
        list($value, $field) = $fields;
        if ( is_object($value) )
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
            !empty(self::$config[$this->get_current_db_name()]['crypt_fields'][$table]) && 
            in_array($field, self::$config[$this->get_current_db_name()]['crypt_fields'][$table])
        ) 
        {
            $value = "AES_ENCRYPT('{$value}', '" . self::$config[$this->get_current_db_name()]['crypt_key'] . "')";
        }
        //json字段
        else if ( 
            !empty(self::$config[$this->get_current_db_name()]['json_fields'][$table]) && 
            in_array($field, self::$config[$this->get_current_db_name()]['json_fields'][$table])
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
                    foreach ($value as $f => $ff)
                    {
                        //转成object是因为枚举数组没法更新
                        $ff    = is_array($ff) ? addslashes(json_encode((object)$ff, JSON_UNESCAPED_UNICODE)) : $ff;
                        //string的才加‘’,否则不加
                        // $ff    = is_string($ff) || !$ff ? "'{$ff}'" : $ff;
                        $tmp[] = "'$.{$f}', " . $this->quote($ff);
                    }

                    $value = 'JSON_SET(' . implode(", ", $tmp) . ')';
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
            // prepare 预处理不需要自己转义，否则会出现双重转义
            // $value = isset($value) ? $this->quote($this->real_escape_string($value)) : 'NULL';
            $value = isset($value) ? $this->quote($value) : 'NULL';
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

            return $this->quote_identifier($value) . ' AS ' . $this->quote_identifier($alias);
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
                $value = str_replace("(" . $match_value . ")", "(" . $quote_value . ")", $value);
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
            return implode('.', array_map([$this, __FUNCTION__], $parts));
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
        // $value = $this->real_escape_string($value);
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
        $this->_select     = [];
        $this->_from       = [];
        $this->_join       = [];
        $this->_on         = [];
        $this->_where      = [];
        $this->_group_by   = [];
        $this->_having     = [];
        $this->_order_by   = [];
        $this->_distinct   = false;
        $this->_limit      = null;
        $this->_limit_by   = null;
        $this->_offset     = null;
        // insert
        $this->_columns    = [];
        $this->_values     = [];
        // update
        $this->_set        = [];
        $this->_atts       = [];

        $this->_as_sql     = false;
        $this->_as_object  = false;
        $this->_as_row     = false;
        $this->_as_field   = false;
        $this->_as_result  = false;

        $this->_parameters = [];
        $this->_max_select_limit = 0;
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

            $this->log_query('autocommit ' . ($mode ? 'true' : 'false'), 'sql', $name);
            return mysqli_autocommit(
                $handle, 
                $mode
            );
        } 
        catch (Exception $e) 
        {
            log::error(sprintf(
                "%s:%s [%s]", 
                $e->getCode(), 
                $e->getMessage(), 
                'autocommit' . '(' . $name . ')'), 
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
        $this->_atts['start'] = true; // 数据库重连可能会导致事务丢失，标记开启了事务不重连
        return $this->autocommit(false);
    }

    public function commit()
    {
        $this->log_query('commit', 'sql', static::$_instance[$this->get_instance_group('master')]);
        return mysqli_commit(static::$_instance[$this->get_instance_group('master')]->_handler());
    }

    public function rollback()
    {
        $this->log_query('rollback', 'sql', $this->get_instance_group('master'));
        return mysqli_rollback(static::$_instance[$this->get_instance_group('master')]->_handler());
    }

    public function end()
    {
        return $this->autocommit(true);
    }

    public function get_exception_trace($e, ?string $sql = null) 
    {
        $ret   = [];
        $ret[] = $e->getMessage();
        $ret[] = sprintf('出错语句：%s', $sql ?? $this->_sql);
        $ret[] = $e->getTraceAsString();
        return implode(PHP_EOL, $ret);
    }    

    /**
    * SQL语句过滤程序（检查到有不安全的语句仅作替换和记录攻击日志而不中断）
    * @param string $sql 要过滤的SQL语句 
    */
    public function filter_sql($sql)
    {
        $clean = $error = '';
        $old_pos = 0;
        $pos = -1;
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
        $fail  = false;
        // sql语句中出现注解
        if (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false)
        {
            $fail = true;
            $error = 'commet detect';
        }
        else
        {
            foreach (self::$rps as $pattern) 
            {
                if (
                    strpos($clean, $pattern) !== false && 
                    preg_match("~(^|[^a-z]){$pattern}($|[^[a-z])~s", $clean) != 0)
                {
                    $fail = true;
                    $error = "{$pattern} detect";
                    break;
                }
            }
        }
        
        // 检测到有错误后记录日志并对非法关键字进行替换
        if ($fail === true)
        {
            $sql = str_ireplace(self::$rps, self::$rpt, $sql);

            // 进行日志
            $gurl = htmlspecialchars( req::cururl() );
            $msg = "{$gurl}\n" . htmlspecialchars( $sql ) . "\n";
            log::warning($msg . $error, 'filter_sql');
        }

        return $sql;
    }

    /**
     * 获取实例名称数组
     * @param  string  $name 实例名称
     * @param  ?string $type master/slave 
     * @return mixed
     */
    public static function get_instance_name($name = null, $type = null)
    {
        $name = self::get_muti_name($name);
        $instance_name = [
            'master'  => $name .'_w',
            'slave'   => $name .'_r',
            'db_name' => $name,
        ];

        return !empty($type) ? $instance_name[$type] : $instance_name;
    }

    /**
     * 获取当前实例组
     * @param string|null $type  master/slave 
     * @return mixed
     */
    public function get_instance_group(?string $type = null)
    {
        return static::get_instance_name($this->_db_name, $type);
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
     * @return bool         返回bool
     */
    public function log_query($sql, $type = 'sql', ?string $curr_conn = null, $used_time = 0)
    {
        // cli下最大记录条数
        $max_len = 1000;
        // 偏移多少后开始截取
        $offset  = 100;
        if ( in_array($type, ['sql', 'prepare_sql', 'query_time']) ) 
        {
            if ( $type == 'sql' ) 
            {
                db::$queries[] = ($curr_conn ? "[{$curr_conn}]: " : '') . $sql;
                $tmp_var       = 'queries';
                // var_dump($sql);
                if ( empty($this->_atts['un_log']) ) 
                {
                    event::trigger(onSql, [$sql, $curr_conn, $used_time]);
                }
            }
            elseif ($type == 'prepare_sql') 
            {
                db::$prepare_queries[] = $sql;
                $tmp_var = 'prepare_queries';
            }
            else
            {
                db::$query_times[] = $sql;
                $tmp_var           = 'query_times';
            }

            if ( IS_CLI && count(db::${$tmp_var}) > $max_len + $offset) 
            {
                db::${$tmp_var} = array_slice(db::${$tmp_var}, -$max_len);
            }
        }
        else if ( isset(db::$affected_rows[$type]) )
        {
            db::$affected_rows[$type] += (int) $sql;
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
