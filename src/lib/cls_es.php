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

use kaliphp\config;
use kaliphp\log;
use kaliphp\util;
use Elasticsearch\ClientBuilder;

/**
 elasticsearch常见使用方法如下：
 $client = cls_es::instance();

 $index = 'geo_test_new111';
 // $arr = $client->get_index($index);
 // var_dump($arr);
 // exit;
 // 
 // $arr = $client->put_mapping($index, [
 //     'name' => ['type' => 'keyword'],
 //     'locataion' => ['type' => 'geo_point']
 // ]);

 $arr = $client->insert($index)
     ->set([
         'name'     => 'test2',
         'location' => ['lat' => 117.296963, 'lon' => 31.818034],
     ])
     ->execute();

 // $arr = $client->select()
 //     ->from($index)
 //     ->where('name', 'test1')
 //     ->execute();

附近的人
 $arr = $client->select()
     ->from($index)
     ->expr('geo_distance', [
         'distance' => '1000km',
         'locataion' => ['lat' => 117.296963, 'lon' => 31.818034]
     ])
     // ->where([
     //     'name' => 'text',
     //     'location' => ['lat' => 10, 'lon' => 20]
     // ])
     // ->filter('name', 'test1')
     ->execute();

 var_dump($client->queries);
 var_dump($arr);
 exit;

 // $status = $client->delete('my_index')
 //     ->where('id', 'my_id341111111')
 //     ->execute();
 // var_dump($status);exit;

 // $tmp = $client->update('my_index')
 //     ->set(['testField' => 'fuck', 'age' => 20])
 //     ->where('id', 'my_id331111111')
 //     ->execute();
 // var_dump($tmp);
 // exit;

 // $tmp = $client->insert('my_index')
 //     ->set(['age' => 1, 'testField' => 'xxx2'], 'my_id331111111111222')
 //     ->set(['age' => 2, 'testField' => 'xxx2'], 'my_id34111111111222221')
 //     ->execute();

 // var_dump($tmp);
 // exit;


 $tmp = $client->select('*')
     ->from('my_index')
     // ->scroll("1m")
     // ->expr('filtered.filter.and.geo_distance_range', ['from' => '0km', 'to' => '80km'])
     // ->where(function($query){
     //     $query->where('testField', 'abc');
     // })
     // ->where('age', 'range', [80, 110])
     // ->where('id', '=', 'my_id3311111111222')
     // ->or_where('testField', '=', '222')
     ->where('testField', 'like', 'x')
     ->filter('age', 'in', [80,100])
     ->or_where('testField', '=', 'x')
     // ->order_by('_id', 'desc')
     // ->offset(25)
     // ->limit(2)
     // ->as_row()
     // ->group_by('age', 'min')
     // ->limit(1)
     ->execute();
 var_dump($client->queries);
 var_dump($tmp);
 exit;
 */
class cls_es
{
    /**
     * @var null
     */
    private $_handler;

    /**
     * @var null
     */
    private $_name;

    /**
     * @var array
     */
    public $where = [];

    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var null
     */
    public $offset = null;

    /**
     * @var null
     */
    public $limit = null;

    /**
     * @var array
     */
    public $order = [];

    /**
     * @var array
     */
    public $aggs = [];

    /**
     * @var string
     */
    public $index = '';

    /**
     * @var string
     */
    public $type = '';

    public $expr = [];

    /**
     * @var string
     */
    public $scroll = '';

    /**
     * @var array
     */
    public $queries = [];

    /**
     * @var array
     */
    private $_set = [];

    /**
     * @var bool
     */
    protected $enable_log_query = false;

    /**
     * @var bool
     */
    protected $_as_row = false;

    /**
     * @var bool
     */
    protected $_as_result = false;

    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @var string
     */
    private $_type = '';

    /**
     * @var array
     */
    public $operators = [
        '='    => 'eq',
        '>'    => 'gt',
        '>='   => 'gte',
        '<'    => 'lt',
        '<='   => 'lte',
    ];


    /**
     * @var array
     */
    protected $dsl_fileds = [
        '_source' => 'columns',
        'query'   => 'where',
        'aggs',
        'sort'   => 'order',
        'size'   => 'limit',
        'from'   => 'offset',
        'index'  => 'index',
        'type'   => 'type',
        'scroll' => 'scroll',
    ];

    /**
     * @var array
     */
    private static $_instances = [];

    public static function _init()
    {
        if ( !class_exists('\Elasticsearch\ClientBuilder') ) 
        {
            throw new \Exception(
                "请先安装Elasticsearch库，https://github.com/elastic/elasticsearch-php", 
                -10010
            );
        }
        
        //self::$config = config::instance('search')->get();
    }

    /**
     * 单例
     * @param string $name
     * @param array config
     * @return db
     */
    public static function instance( $name = 'search', array $config = null )
    {
        if (!isset(self::$_instances[$name]))
        {
            self::$_instances[$name] = new self($name, $config);
        }

        return self::$_instances[$name];
    }

    public function __construct($name = 'search', $config = null)
    {
        $this->_name   = $name;
        $this->_config = $config ? $config : self::$config;
    }

    /**
     * 实例化一个elasticsearch对象
     * @param  array  $config 
     * @return object         
     */
    private function _handler(array $config = [])
    {
        if ( !is_object($this->_handler) ) 
        {
            $config = $config ? $config : $this->_config;
            $this->_handler = ClientBuilder::create();

            //设置服务器连接信息
            if ( isset($config['host']) ) 
            {
                $this->_handler->setHosts($config['host']);
            }

            //重连次数
            if ( isset($config['retry_times']) ) 
            {
                $this->_handler->setRetries($config['retry_times']);
            }

            //设置连接池
            if ( isset($config['connection_pool'])) 
            {
                $this->_handler->setConnectionPool($config['connection_pool']);
            }

            //设置序列化器
            if ( isset($config['serializer']) ) 
            {
                $this->_handler->setConnectionPool($config['serializer']);
            }

             $this->_handler = $this->_handler->build();
        }

        return $this->_handler;
    }

    /**
     * index 别名
     * @param  string $index 索引名称
     * @return $this
     */
    public function from($index)
    {
        return $this->index($index);
    }


    /**
     * 指定查询的索引
     * @param  string $index 索引名称
     * @return $this
     */
    public function index($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * 指定类型
     * @param  string $index 类型名称
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return up to "LIMIT ..." results
     *
     * @param   integer  $number  maximum results to return
     *
     * @return  $this
     */
    public function limit(int $value)
    {
        $this->limit = $value;
        return $this;
    }

    public function as_row()
    {
        $this->_as_row = true;
        $this->limit(1);

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

    /**
     * 获取一行数据
     * @param  int    $value 
     * @return $this
     */
    public function offset(int $value)
    {
        $this->offset = $value;
        return $this;
    }

    /**
     * 指定排序
     * @param  string $field 
     * @param  string $sort  
     * @return $this
     */
    public function order_by(string $field, $sort)
    {
        $this->order[$field] = $sort;
        return $this;
    }

    //不知道为何聚合算法，不起作用
    public function group_by($field, $type = null)
    {
        return $this->agg_by($field, $type);
    }

    /**
     * 聚合计算
     * 多次调用agg_by会嵌套调用
     * @param  string $field 
     * @param  string $type  
     * @return $this
     */
    public function agg_by($field, $type = null)
    {
        //确保field为关联数组
        $aggs_item = is_array($field) ? $field : [$field => $type];
        $this->aggs[] = $aggs_item;

        return $this;
    }

    /**
     * 设置游标
     * @param  string $scroll 1m...游标保留时间
     * @return $this
     */
    public function scroll(string $scroll)
    {
        $this->scroll = $scroll;
        return $this;
    }

    /**
     * 需要解析的where
     * @param  参数不定，支持keypath
     * @return $this
     */
    public function expr()
    {
        $args = func_get_args();
        if ( count($args) == 1 ) 
        {
            $this->expr[] = $args[0];
        }
        else
        {
            $this->expr[$args[0]] = $args[1];
        }

        return $this;
    }

    /**
     * 查询字段
     * @param  mix $columns 
     * @return $this
     */
    public function select($columns = null)
    {
        $this->_type = __function__;
        $columns = $columns == '*' ? null : $columns;
        $columns && $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * 插入文档
     * @param  string $table 查询的index
     * @param  mix $columns  插入的字读啊安，一般不需要
     * @return $this
     */
    public function insert($table = null, array $columns = null)
    {
        $this->_type = __function__;
        if ( $table ) 
        {
            $this->index($table);
        }

        if ( $columns ) 
        {
            $this->columns = $columns;
        }

        return $this;
    }

    /**
     * 更新文档
     * @param  string $table 更新index
     * @return $this
     */
    public function update($table = null)
    {
        $this->_type = __function__;
        if ($table)
        {
            $this->index = $table;
        }

        return $this;
    }

    /**
     * 删除文档
     * @param  string $table 更新index
     * @return $this
     */
    public function delete($table = null, $id = null)
    {
        $this->_type = __function__;
        if ($table)
        {
            $this->index = $table;
        }

        if ( $id ) 
        {
            $this->_set = [
                'id' => $id
            ];
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
    public function set()
    {
        $args = func_get_args();
        switch ($this->_type) {
            case 'insert':
                @list($data, $id, $key) = $args;
                if(empty($data['id']))
                {
                    $data['id'] = $id ? $id : (isset($data[$key]) ? $data[$key] : uniqid());
                } 
                $this->_set[] = $data;

                break;
            case 'update':
                @list($data, $id) = $args;
                $this->_set = [
                    'id'   => $id,
                    'body' => ['doc' => $data],
                ];
                break;            
            default:
                $this->_set = array_merge($this->_set, $args);
                break;
        }

        return $this;
    }

    /**
     * filter过滤（不带评分，性能比where好）
     * @param $column
     * @param null   $operator
     * @param null   $value
     * @param string $leaf
     * @param string $boolean
     * @return $this
     */
    public function filter($column, $operator = null, $value = null, $leaf = 'term', $boolean = 'and')
    {
        return $this->where(
            $column, $operator, $value, 
            $leaf, $boolean, true
        );
    }

    /**
     * @param $column
     * @param null   $operator
     * @param null   $value
     * @param string $leaf
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $leaf = 'term', $boolean = 'and', $filter = false)
    {
        if ( is_array($column) ) 
        {
            foreach($column as $field => $col)
            {
                call_user_func([$this, 'where'], $field, $col, null, $leaf, $boolean, $filter);
            }

            return $this;
        }
        else if ($column instanceof \Closure) 
        {
            return $this->where_nested($column, $boolean);
        }
        else if ( func_num_args() === 2 || !$value ) 
        {
            list($value, $operator) = [$operator, '='];
        }

        if (is_array($operator) ) 
        {
            list($value, $operator) = [$operator, null];
        }

        //尽量少用模糊查询，效率非常慢
        $suffix = $prefix = '';
        if ( in_array($operator, ['like', '*=*']) ) 
        {
            $suffix = $prefix = '*';
            $leaf   = 'wildcard';
        }
        else if( $operator == '*=' )
        {
            $suffix = '*';
            $leaf   = 'wildcard';
        }
        else if( $operator == '=*' )
        {
            $suffix = '*';
            $leaf   = 'wildcard';
        }
        else if ($operator !== '=') 
        {
            $leaf = 'range';
        }

        if (is_array($value) && $leaf === 'range') 
        {
            $value = [
                $this->operators['>='] => $value[0],
                $this->operators['<='] => $value[1],
            ];
        }

        if ($suffix || $prefix) 
        {
            if (is_array($value)) 
            {
                foreach ($value as $index => $item) 
                {
                    if (is_string($item)) 
                    {
                        $value[$index] = $prefix . $item . $suffix;
                    }
                }
            } 
            else 
            {
                $value = ($prefix ?? "") . $value . ($suffix ?? "");
            }
        }

        $type = $filter ? 'filter' : 'basic';
        if ( !empty($operator) && isset($this->operators[$operator]) ) 
        {
            $operator = $this->operators[$operator];
        }
 
        $this->where[] = compact(
            'type', 'column', 'leaf', 
            'value', 'boolean', 'operator'
        );

        return $this;
    }

    /**
     * 创建索引
     * @param  string $index_name 索引名称
     * @param  array  $config     配置数组
     * @return array
     */
    public function create_index($index_name, $config = [])
    {
        $params = [
            'index' => $index_name,
            'body' => [
                'settings' => array_merge([
                    'number_of_shards'   => 5,
                    'number_of_replicas' => 0
                ], $config)
            ]
        ];

        return $this->execute($params, 'create', $this->_handler()->indices());
    }

    /**
     * 获取索引
     * @param  string $index_name 索引名称
     * @return array
     */
    public function get_index($index_name)
    {
        return $this->execute(['index' => $index_name], 'get', $this->_handler()->indices());
    }

    /**
     * 判断索引是否存在
     * @param  string $index_name 索引名称
     * @return array
     */
    public function exists_index($index_name)
    {
        return $this->execute(['index' => $index_name], 'exists', $this->_handler()->indices());
    }

    /**
     * 删除索引
     * @param  string $index_name 索引名称
     * @return array
     */
    public function delete_index($index_name)
    {
        return $this->execute(['index' => $index_name], 'delete', $this->_handler()->indices());
    }

    /**
     * 配置mapping
     * @param  string $index_name 索引名称
     * @param array  配置数组
     * @return array 
     */
    public function put_mapping($index_name, array $params, $type = null)
    {
        $body = isset($params['properties']) ? $params : ['properties' => $params];
        $params = [
            'index' => $index_name,
            'type'  => $type ? $type : $this->type,
            'body'  => $body,
        ];

        return $this->execute($params, 'putMapping', $this->_handler()->indices());
    }


    /**
     * 记录query的执行结果
     * @param  string       记录内容
     * @return bool         返回bool
     */
    public function log_query($params)
    {
        //非cli/调试模式/本机记录sql和时间，方便调试
        if ( 
            PHP_SAPI != 'cli' || 
            (defined('SYS_DEBUG') && SYS_DEBUG === true) || 
            (defined('IP') && IP === '127.0.0.1') 
        )
        {
            $this->queries[] = $params;
        }

        return true;
    }

    /**
     * 重置类属性
     * @return $this
     */
    public function reset()
    {
        foreach(['where', 'columns', 'order', 'aggs', 'expr'] as $f)
        {
            $this->$f = [];
        }

        foreach(['offset', 'limit', 'index', 'type', '_type', 'scroll'] as $f)
        {
            $this->$f = '';
        }

        $this->_as_row   = false;
        $this->as_result = false;
        return $this;
    }

    /**
     * 解析成多维数组
     * @param  string $key         keypath
     * @param  mix    $val         默认空值
     * @param  string $default_dsl 默认前缀
     * @return array  返回一个多维数组
     */
    public function compile_muti_array($key, $val = null, $default_dsl = 'query')
    {
        $default_dsl && $key = $default_dsl.'.'.$key;
        $keys  = explode('.', $key);
        $count = count($keys);

        $array = [];
        if ( $count > 6 ) 
        {
            throw new \Exception("最多只能生成6维数组", -10010);
        }

        switch ( count($keys) )  
        {
            case 1:
                $array[$keys] = $val;
                break;
            case 2:
                $array[$keys[0]][$keys[1]] = $val;
                break;
            case 3:
                $array[$keys[0]][$keys[1]][$keys[2]] = $val;
                break;
            case 4:
                $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $val;
                break;
            case 5:
                $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]] = $val;
                break;
            case 6:
                $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]][$keys[5]] = $val;
                break;
        }
    
        return $array;
    }

    /**
     * @param $field
     * @param $value
     * @param string $boolean
     * @return $this
     */
    public function where_match($field, $value, $boolean = 'and')
    {
        return $this->where($field, '=', $value, 'match', $boolean);
    }

    /**
     * @param $field
     * @param $value
     * @param string $boolean
     * @return $this
     */
    public function or_where_match($field, $value, $boolean = 'or')
    {
        return $this->where_match($field, $value, $boolean);
    }

    /**
     * @param $field
     * @param $value
     * @param string $boolean
     * @return $this
     */
    public function where_term($field, $value, $boolean = 'and')
    {
        return $this->where($field, '=', $value, 'term', $boolean);
    }

    /**
     * @param $field
     * @param array $value
     * @return $this
     */
    public function where_in($field, array $value)
    {
        return $this->where(function () use ($field, $value) {
            array_map(function ($item) use ($field) {
                $this->or_where_term($field, $item);
            }, $value);
        });
    }

    /**
     * @param $field
     * @param array $value
     * @return $this
     */
    public function or_where_in($field, array $value)
    {
        return $this->or_where(function () use ($field, $value) {
            array_map(function ($item) use ($field) {
                $this->or_where_term($field, $item);
            }, $value);
        });
    }

    /**
     * @param $field
     * @param $value
     * @param string $boolean
     * @return $this
     */
    public function or_where_term($field, $value, $boolean = 'or')
    {
        return $this->where_term($field, $value, $boolean);
    }

    /**
     * @param $field
     * @param null   $operator
     * @param null   $value
     * @param string $boolean
     * @return $this
     */
    public function where_range($field, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($field, $operator, $value, 'range', $boolean);
    }

    /**
     * @param $field
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function or_where_range($field, $operator = null, $value = null)
    {
        return $this->where($field, $operator, $value, 'or');
    }

    /**
     * @param $field
     * @param array  $values
     * @param string $boolean
     * @return $this
     */
    public function where_beetween($field, array $values, $boolean = 'and')
    {
        return $this->where($field, null, $values, 'range', $boolean);
    }

    /**
     * @param $field
     * @param array $values
     * @return $this
     */
    public function or_where_beetween($field, array $values)
    {
        return $this->where_beetween($field, $values, 'or');
    }

    /**
     * @param $field
     * @param null   $operator
     * @param null   $value
     * @param string $leaf
     * @return $this
     */
    public function or_where($field, $operator = null, $value = null, $leaf = 'term')
    {
        if (func_num_args() === 2) 
        {
            list($value, $operator) = [$operator, '='];
        }

        return $this->where($field, $operator, $value, $leaf, 'or');
    }

    /**
     * @param \Closure $callback
     * @param $boolean
     * @return $this
     */
    public function where_nested(\Closure $callback, $boolean)
    {
        call_user_func($callback, $this);
        return $this;
    }

    /**
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     */
    public function execute(array $params = [], $method = 'search', $handler = null)
    {   
        if ( $this->_type ) 
        {
            $params  = array_merge($this->compile(), $params);
        }

        if ( isset($params['method']) ) 
        {
            $method = $params['method'];
            unset($params['method']);
        }

        try 
        {
            $return = null;
            $this->log_query([
                'method' => $method,
                'params' => $params,
            ]);

            $handler = $handler ? $handler : $this->_handler();
            $result = call_user_func([$handler, $method], $params);

            if ( $this->_as_result) 
            {
                return $result;
            }

            switch ($this->_type) 
            {
                case 'select':
                    $tmp = [];
                    $return = [
                        'total'      => util::get_value($result, 'hits.total', 0),
                        'max_score'  => util::get_value($result, 'hits.max_score', 0),
                        '_scroll_id' => util::get_value($result, '_scroll_id'),
                    ];

                    if ( $return['total'] > 0 ) 
                    {
                        foreach($result['hits']['hits'] as $row)
                        {
                            $tmp[] = array_merge(['id' => $row['_id']], $row['_source']);
                        }
                    }

                    $return['data'] = $this->_as_row ? reset($tmp) : $tmp;
                    //是否有聚合计算
                    if ( !empty($this->aggs) ) 
                    {
                        $return['aggs'] = util::get_value($result, 'aggregations');
                    }
                    break;
                case 'insert':
                    $return = ['insert_ids' => null, 'update_ids' => null, 'total' => 0];
                    if ($method == 'bulk') 
                    {
                        array_map(function($row) use (&$return) {
                            if ( !empty($row['index']) ) 
                            {
                                $row = $row['index'];
                                if (isset($row['result']))
                                {
                                    if('created' == $row['result'])
                                    {
                                        $return['insert_ids'][] = $row['_id'];
                                    }
                                    elseif('updated' == $row['result'])
                                    {
                                        $return['update_ids'][] = $row['_id'];
                                    }
                                }

                                $return['total']++;
                            }
                        }, $result['items']);
                    }
                    else
                    {
                        $return = [
                            'insert_ids' => util::get_value($result, '_id'),
                            'total'      => util::get_value($result, '_shards.successful'),
                        ];
                    }

                    $return = array_values($return);

                    break;
                case 'delete':
                    $return = util::get_value($result, 'result') == 'deleted';
                    break;
                case 'update':
                    $return = util::get_value($result, 'result') == 'updated';
                    break;
                default:
                    $return = $result;
            }
        } 
        catch (\Exception $e) 
        {
            // var_dump($e->getMessage());
            log::error('elastisearch error:'.$e->getMessage());
            $return = [];
        }
        
        $this->reset();
        return $return;
    }

    /**
     * Compile the SQL query and return it.
     *
     * @return  string
     */
    public function compile()
    {
        $body = [];
        foreach($this->dsl_fileds as $field => $field_alias)
        {
            if (!empty($this->$field_alias)) 
            {
                $method = '_compile_'.$field_alias;
                $field_val = method_exists($this, $method) ? 
                    call_user_func([$this, $method]) : 
                    $this->$field_alias;

                $body[is_numeric($field) ? $field_alias : $field] = $field_val;
                if ($field_alias == 'aggs' && $field_val) 
                {
                    $body['size'] = 0;
                }
            }
        }

        if ( $this->expr ) 
        {
            foreach ($this->expr as $key => $item) 
            {
                if ( !is_numeric($key) ) 
                {
                    $body = util::array_merge_multiple(
                        $body, 
                        $this->compile_muti_array($key, $item)
                    );
                }
                else if ($item instanceof \Closure) 
                {
                    $this->where($item);
                }
                else if ( is_array($item) ) 
                {
                    $body = util::array_merge_multiple($body, $item);
                }
            }
        }

        if ( empty($body['query']) ) 
        {
            unset($body['query']);
        }
        
        $func = 'get_compiled_'.$this->_type;
        if ( !method_exists($this, $func) ) 
        {
            throw new \Exception("Error Method", -10010);
        }

        return call_user_func([$this, $func], $body);
    }

    /**
     *
     * @return array
     */
    public function get_compiled_select($body)
    {
        foreach (['index', 'type', 'scroll'] as $f) 
        {
            if ( isset($body[$f]) ) 
            {
                $$f = $body[$f];
                unset($body[$f]);
            }
        }

        $method = 'search';
        $params = compact('body', 'index', 'method');
        !empty($type) && $params['type'] = $type;

        if ( isset($scroll) ) 
        {
            $params['scroll'] = $scroll;
        }
    
        return $params;
    }

    /**
     *
     * @return array
     */
    public function get_compiled_insert($body)
    {
        $ids = array_column($this->_set, 'id');
        $method = 'create';

        if ( count($ids) > 1 ) 
        {
            $params = [];
            $method = 'bulk';
            foreach($this->_set as $row)
            {
                $params['body'][] = [
                    'index' => [
                        '_index' => $body['index'],
                        '_id'    => $row['id'],
                    ]
                ];

                $params['body'][] = $row;
            }

            return array_merge(
                array_merge($params, $body), 
                ['method' => 'bulk']
            );
        }
        else
        {
            return array_merge(
                array_merge(reset($this->_set), $body), 
                ['method' => 'create']
            );
        }
    }

    /**
     *
     * @return array
     */
    public function get_compiled_update($body)
    {
        return array_merge(
            array_merge($this->_set, $body), 
            ['method' => 'update']
        );
    }

    /**
     *
     * @return array
     */
    public function get_compiled_delete($body)
    {
        return array_merge(
            array_merge($this->_set, $body), 
            ['method' => 'delete']
        );
    }


    /**
     *  构建aggs的参数
     *  $this->aggs为数组，多个元素会嵌套调用，请注意
     * @return array
     */
    public function _compile_aggs(): array
    {
        $aggs = [];
        foreach ($this->aggs as $k => $aggs_item) 
        {
            foreach ($aggs_item as $field => $type) 
            {
                if(is_array($type))
                {
                    //数组的type,表示携带了复杂结构，直接按传入的来
                    $field_name = $field;
                }
                else
                {
                    $field_name  = $field .'_'. $type;//固定为此格式
                    $type = [$type => ['field' => $field]];
                }
                $aggs[$field_name] = $type;
            }

        }
        return $aggs;
    }

    /**
     *
     * @return string
     */
    public function _compile_index(): string
    {
        return is_array($this->index) ? implode(',', $this->index) : $this->index;
    }

    /**
     *
     * @return array
     */
    public function _compile_order(): array
    {
        $order = [];
        foreach ($this->order as $field => $order_item) 
        {
            $order[$field] = is_array($order_item) ? $order_item : ['order' => $order_item];
        }

        return $order;
    }

    /**
     *
     * @return array
     */
    protected function _compile_where(): array
    {
        $where_groups = $this->_compile_where_group($this->where);
        $operation = count($where_groups) === 1 ? 'must' : 'should';
        $bool = [];

        foreach ($where_groups as $where) 
        {
            $must = $filter = [];
            foreach ($where as $where) 
            {
                //支持id放where中
                if ( in_array($this->_type, ['delete', 'update']) && $where['column'] == 'id' ) 
                {
                    $this->_set['id'] = $where['value'];
                    return $bool;
                }
                else if( $where['type'] == 'filter' )
                {
                    $filter[] = $this->_compilee_where_leaf(
                        $where['leaf'], $where['column'], 
                        $where['operator'], $where['value']
                    );
                }
                else if ($where['type'] === 'nested') 
                {
                    $must[] = $this->_compile_where($where['query']);
                }
                else 
                {
                    $must[] = $this->_compilee_where_leaf(
                        $where['leaf'], $where['column'], 
                        $where['operator'], $where['value']
                    );
                }
            }

            //filter过滤
            if ( $filter ) 
            {
                // var_dump($filter);exit;
                $bool['bool']['filter'][] = count($filter) === 1 ? 
                    array_shift($filter) : 
                    ['bool' => ['filter' => $filter]];
            }

            //带评分的查询
            if ( $must ) 
            {
                $bool['bool'][$operation][] = count($must) === 1 ? 
                    array_shift($must) : 
                    ['bool' => ['must' => $must]];
            }
        }

        return $bool;
    }

    /**
     * @param string      $leaf
     * @param string      $column
     * @param string|null $operator
     * @param $value
     *
     * @return array
     */
    protected function _compilee_where_leaf(string $leaf, string $column, string $operator = null, $value): array
    {
        if ($leaf === 'range') 
        {
            return [
                $leaf => [
                    $column => is_array($value) ? $value : [$operator => $value],
                ]
            ];
        }
        else
        {
            return [$leaf => [$column => $value]];
        } 
    }

    /**
     * @param array $where
     * @return array
     */
    protected function _compile_where_group(array $where): array
    {
        $or_index = (array) array_keys(array_map(function ($where) {
            return $where['boolean'];
        }, $where), 'or');

        $group = [];
        $last_ndex = $init_index = 0;
        foreach ($or_index as $index) 
        {
            $group[] = array_slice($where, $init_index, $index - $init_index);
            $init_index = $index;
            $last_ndex = $index;
        }

        $group[] = array_slice($where, $last_ndex);
        return $group;
    }

}