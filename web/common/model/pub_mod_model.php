<?php
namespace common\model;

use common\extend\pub_func;
use kaliphp\db;
use kaliphp\lib\cls_arr;
use kaliphp\log;
use kaliphp\req;
use kaliphp\util;
use kaliphp\cache;
use kaliphp\config;
use kaliphp\lib\cls_filter;
use kaliphp\lib\cls_bbcode;

/**
 * 这个类一般都作为基类被继承(数据库相关操作，尽量用里面的函数，避免每个人一套，造成日后维护艰难)
 * **************************************常用操作***************************************
 * 1.插入单条数据
 * $data = self::insert(['uid' => 2]);
 * 2.插入多条数据
 * $data = self::insert([['uid' => 2], ['uid' => 3]]);
 * 3.更新单条数据
 * $data = self::update(['uid' => 2], ['uid' => 2]);
 * 4.已存在当前主键/unique key 更新，没有则插入
 * $data = self::insert(
 *     ['uid' => 2, 'status' => 3], ['uid' => 2, 'status' => 2],
 *     ['dups' => ['status' => 3]] //values(`xx`)表示新值 `xx`则是旧值
 * );
 * 5.批量更新，必须指定对比字段 _cmp_field_ =依赖更新的字段，一般是pk,更新的数组里面的字段可以不一致
 * $data = self::update(
 *     ['uid' => 2, 'status' => 3], ['uid' => 2, 'age' => 2],
 *     ['_cmp_field_' => 'xx'....] 
 * );
 * 6.删除数据
 * $data = self::delete(['uid' => 2]);
 * 7.查找某一条数据
 * $data = self::find([
 *   'where' => ['uid' => 1, 'status[in]' => [2, 3]]
 * ]);
 * 如果 find/lists/find/count/sum/avg这些函数把第二个参数指定为ture,可以省掉一维where，也就是
 * $data = self::find(['uid' => 1, 'status[in]' => [2, 3]，true) 得到的结果是一样的
 * 8.where字段中支持操作符 
 * %$% => like '%xx%'
 * %$  => like '%xx'
 * $%  => like 'xx%'
 * >< 并且值为长度为2的枚举  => 'a' > arr[0] and 'a' < $arr[1]
 * !=  => 不等于
 * >      大于
 * <      小于
 * in  => in (xx1, xx2) 这个一般不需要，只要值为数组，就会自动用in
 * 一个字段使用多个，可以使用 & 连接起来，比如 age>=1 <=10：
 * self::lists([
 *     'where' => ['age[>=&<=]' => [1, 10]]
 * ])
 * 9.联表查找某一条数据
 * $data = self::find([
 *   'table' => '#PB#_member', //如果这个是self::$table可以不用写
 *   'where' => ['status' => 1], //支持字段中加[操作符]比如 ['status[%]' => xxx]
 *   'join' => [
 *       //left join
 *       'xx_a_table[left]' => [
 *           'xx_a_table.uid' => 'uid',
 *           'uid' => 'uid'
 *       ],
 *       //join
 *       'xx_b_table' => [
 *           'uid' => 'uid',
 *           'currency_code' => db::expr("'cny'")
 *       ]
 *   ]
 * ]);
 * 10.数据列表
 * $data = self::lists([
 *   'count' => 1, //需要统计总数才需要传
 *   'where' => ['uid' => 1, 'status' => 2],
 * ]);
 * 11.联表数据列表
 * $data = self::lists([
 *   'table' => '#PB#_member', //如果这个是self::$table可以不用写
 *   'where' => ['status' => 1],
 *   'page' => 1,
 *   'pagesize' => 10,
 *   'count' => true,
 *   'join' => [
 *       //left join
 *       'xx_a_table[left]' => [
 *           'xx_a_table.uid' => 'uid',
 *           'uid' => 'uid'
 *       ],
 *       //join
 *       'xx_b_table' => [
 *           'uid' => 'uid', //都没有写表名，field1会默认使用table1,field2会使用主表
 *           'currency_code' => db::expr("'cny'")
 *       ]
 *   ]
 * ]);
 *
 * 12.一些复杂的sql,比如统计也尽量不要自己拼，不好维护，而且还容易注入，这个类，已经做了查询条件/table/join table的子查询支持，比如
 * class mod_test extends pub_mod_model
 *   {
 *       public static $table = 'dx_test';
 *   }
 *
 *   $sql = mod_test::lists([
 *       'fields' => ['game_id'],
 *       'table'  => 'dx_stat_test',
 *       'where'  => ['game_id[>]' => 0],
 *       'as_sql' => '',
 *   ]);
 *
 *   $join_sql = mod_test::lists([
 *       'fields' => ['game_id'],
 *       'table'  => 'dx_stat_test',
 *       'where'  => ['game_id[>]' => 0],
 *       'as_sql' => 'b',
 *   ]);
 *   $arr = mod_test::lists([
 *       'table'  => mod_test::lists(['as_sql' => 'a']),
 *       'where'  => ['id[not in]' => mod_test::expr($sql)],
 *       'join'   => [$join_sql . '[left]' => ['game_id' => 'id']]
 *   ]);
 *   var_dump($arr);
 *   exit;
 *  最后生成：SELECT `a`.* FROM (SELECT * FROM `dx_test`) a LEFT JOIN (SELECT `game_id` FROM `dx_stat_test` WHERE `game_id` > '0') AS `b` ON (`b`.`game_id` = `a`.`id`) WHERE `a`.`id` NOT IN (SELECT `game_id` FROM `dx_stat_test` WHERE `game_id` > '0')
 *  嵌套各种or和and
 *  
 * $sql = mod_test::lists([
 *       'fields' => ['game_id'],
 *       'table'  => 'dx_stat_test',
 *       'where'  => [
 *           'game_id[>]' => 1, 
 *           'game_id[>=]'=> 2, 
 *           'or' => [
 *               'game_id[>]'  => 3, 
 *               'game_id[!=]' => 4,
 *               'and' => [
 *                   'game_id[>]' => 5, 
 *                   'or'         => ['game_id[>]' => 6, 'game_id[<]' => 7],
 *                   'and'        => ['hour' => 1, 'or' => ['hour' => 1, 'hour[>]' => 10]]
 *                ] 
 *           ]
 *       ],
 *   ]);
 * 最后生成sql：SELECT `game_id` FROM `dx_stat_test` WHERE (`game_id` > '3' OR `game_id` != '4' OR ((`game_id` > '5' AND ((`game_id` > '6' OR `game_id` < '7')) AND ((`hour` = '1' AND ((`hour` = '1' OR `hour` > '10'))))))) AND `game_id` > '1' AND `game_id` >= '2'
 *
 * 如果遇到一些超级无敌变态的查询，也可以使用expr包装一下，比如 ['a' => self::expr('fuck fuck...')]
 * 
 * 13.如果要取不同表的字段，可以在fields中指定
 * $data = self::lists([
 *   'fields' => [
 *       '' => ['uid'], // key为空默认主表
 *       'xx_a_table' => ['name', 'code'],
 *       'xx_b_table' => 'amount,total_amount'
 *   ]
 *   'where' => ['status' => 1],
 *   'page' => 1,
 *   'pagesize' => 10,
 *   'count' => true,
 *   'join' => [
 *       //left join
 *       'xx_a_table[left]' => [
 *           'xx_a_table.uid' => 'uid',
 *           'uid' => 'uid'
 *       ],
 *       //join
 *       'xx_b_table' => [
 *           'uid' => 'uid',
 *           'currency_code' => db::expr("'cny'")
 *       ]
 *   ]
 * ]);
 *
 * 14.调用其他数据库，只要声明一下 $module_db就可以了，例如：
 * //config_file为空会到database中取key为name配置，取不当尝试取为name的配置，还没找到，会使用默认库
 * //config_file不为空，不带:，会认为整个字符串为配置文件
 * //如果有则任务:前面是配置文件，后面为配置的key,
 *  public static $module_db = [
 *      'name' => 'stat_db',                 //实例名称
 *      'config_file' => 'database:stat_db'  //配置文件名称（config文件：key）
 *  ];
 *
 * //会先尝试从默认数据库配置中找stat_db的配置，
 * //没找会找 名为 stat_db.php的配置文件
 *  static $module_db = ['name' => 'stat_db'];config_file一般留空即可
 *
 * 15.子语句当table
 *  sub_sql = self::lists([
 *       'limit'   => 100,
 *       'as_sql' => 'sub',
 *   ]);
 *   $tmp = self::find([
 *       'table' => self::expr($sub_sql),
 *       'where' => array_intersect($D, [
 *           'gameId'     => null,
 *           'serverType' => null,
 *           'serverIdx'  => null,
 *           'level'      => null,
 *       ])
 *   ]);
 *
 * 16.子语句当表的join
 * $sql = mod_test::lists([
 *     'table'  => 'dx_stat_test',
 *     'as_sql' => 'bb',
 * ]);
     *
 * $arr = mod_test::lists([
 *     'join' => [$sql.'[left]' => ['game_id' => 'id']]
 * ]);
 *
 * 17.条件使用子语句
 *  $where_sql = mod_test::lists([
 *      'as_sql' => '', //这里用一个空的字符串，否则会带上别名，查询语法错误
 *  ])
 *  * $sql = mod_test::lists([
 *     'table'  => 'dx_stat_test',
 *     'where' => ['field' => self::expr($sql)],
 * ]);
 *
 * 18.database_方法也可以指定数据库
 * 注意：如果是有加密字段的表，需要在系统配置里面加一个 table_cache_time的key,加字段的时候，修改一下这个值
 * 否则字段将取不到
 * 每个函数的想起参数请移步到相关函数
 ******************************************end**************************************
**/

/**
 * 基础类
 */
class pub_mod_model
{
    const YES    = 1;
    const NO     = 0;
    const MD_KEY = 'BASE_MD_KEY';

    public static
        $pk                = '', //主键
        $table             = '', //表名
        $pagesize          = 10, //页码
        $fields            = [], //查询字段，不设置会自动分析表结构获取表名
        $table_ifnos       = [], //表信息
        $exclude_fields    = [], //查询忽略的字段，自动获取字段实用化
        $config            = [], //配置信息
        $cache_time        = 86400,
        $enable_slave      = false,//默认是false
        //如果需要默认加载其他数据库配置，继承类里面定义 ['实例子名称', '配置名称名称']
        $module_db         = [ 
            'name'           => null,   //实例名称
            'config_file'    => null,   //配置文件名称 database:xxx or xxxx
            'set_default_db' => false,  //是否每次查询都设置当前模块数据库为默认数据库
            'slave_index'    => null,   //配置将使用指定的index,0开始
            'auto_load_db'   => false,  //是否自动加载模块数据库
        ],
        $unknow_err_status = -1211, // 未知错误,一般都是数据库死锁
        $dead_lock_status  = -1213, // 死锁全局返回状态
        $func_not_fund     = -1214, // 方法不存在
        $msg_maps          = [],    // 错误映射
        $_module_dbs       = [],
        $_default_dbs      = [],   //多库第一次设置的时候保存
        $_load_tables      = [],
        $muti_dbs          = [],   // 多库的时候用到
        //$data_rules        = [],
        $muti_trans        = [],
        $data_rules        = [],
        $auto_data_rule    = false,
        $max_pagesize      = 1000,
        $data              = [], //model往外传数据，可以用这个属性
        $bool_map          = [self::YES => '是', self::NO => '否'],
        $func_allow_fields = [ // conds 允许的字段
            'where', 'count', 'page', 'pagesize', 'next_page', 'formatter', 
            'fields', 'lock', 'share', 'table', 'is_master', 'filter_where',
            'alias', 'force_index', 'index', 'id', 'order_by', 'group_by',
            'offset', 'limit', 'limit_by', 'having', 'total', 'as_sql',
            'join', 'pk', 'func', 'union', 'db_table', 'batch_num', 'batch_field',
            'is_union_table', 'count_fields', 'cache_data', 'cache_total', 'nearly_count',
            'db_row_fn',
        ];

    /**
     * 加载class的时候会执行一次
     * @return   void
     */
    public static function _init()
    {
        //是否自动加载模块数据库
        if( !empty(static::$module_db['auto_load_db']) )
        {
            db::init_db(
                static::$module_db['name'],
                static::$module_db['config_file'],
                static::$module_db['set_default_db'],
                static::$module_db['slave_index']
            );
        }

        // 表过滤规则
        if ( static::$data_rules ) 
        {
            static::$data[static::class]['data_rule'] = static::$data_rules;
        }

        //保证每个类都是用自己的配置
        static::set_mod_db(static::$module_db);
        //static::$_module_dbs[static::class] = static::$module_db;
    }

    /**
     * cli模式加上进程ID,防止多进程实例串行
     * @param  string $name 实例名称
     * @return string       cli下带进程号的实例名称
     */
    public static function get_muti_name(string $name):string
    {
        if ( PHP_SAPI == 'cli'  && function_exists('posix_getpid') )
        {
            $pid = ':' . posix_getpid();
            $len = strlen($pid);
            if ( substr($name, -$len) != $pid ) 
            {
                $name .= $pid;
            }
        }
        
        return $name;
    }

    /**
     * 设置模块db信息
     * @param    array      $module_db
     */
    final public static function set_mod_db(array $module_db)
    {
        $name = $module_db['name'] ?? null;
        // 默认数据库，防止同一个类多个数据库操作的时候，找不到默认数据库配置
        if ( !isset(static::$_module_dbs[static::class]) ) 
        {
            static::$_default_dbs[static::class] = $module_db;
        }

        static::$_module_dbs[static::class] = static::$_module_dbs[$name] = $module_db;
        // 跨库联表判断是否加上库名
        if ( static::$table ) 
        {
            static::$_load_tables[static::$table][$module_db['name'] ?? null] = 1;
        }

        return true;
    }

    /**
     * 还原成默认的数据库
     */
    final public static function set_default_db()
    {
        $module_db = static::$_default_dbs[static::class] ?? static::$module_db;        
        return $module_db ? static::set_mod_db($module_db) : false;
    }

    /**
     * 设置模块数据库配置(set_mod_db别名函数)
     * @param    string       $name          
     * @param    string|null  $config_file   
     * @param    bool|boolean $set_default_db
     */
    final public static function set_module_config(
        ?string $name        = null, 
        ?string $config_file = null, 
        bool $set_default_db = false,
        $slave_index         = null
    )
    {
        return static::set_mod_db([
            'name'           => $name ?? static::$module_db['name'],
            'config_file'    => $config_file ?? static::$module_db['config_file'] ?? null,
            'set_default_db' => $set_default_db,
            'slave_index'    => $slave_index,
        ]);
    }

    /**
     * 获取当前数据库配置
     * @return   array
     */
    final public static function get_current_db_config()
    {
        return static::$_module_dbs[static::class] ?? static::$module_db;
    }

    /**
     * 多库场景下，设置当前数据库
     * 在_init 对 static::$muti_dbs赋值
     * @param    mixed        $db_key     
     * @param    bool|boolean $is_multi_db 是否为多库，false的话，效果和set_mod_db一样
     */
    public static function set_current_db($db_key, bool $is_multi_db = true)
    {
        if( $is_multi_db && static::$muti_dbs && !isset(static::$muti_dbs[$db_key]) )
        {
            // if ( !static::set_default_db() ) 
            // {
                static::exception("数据库配置[{$db_key}]不存在", -1);
            // }

            // return true;
        }

        static::set_mod_db(['name' => static::$muti_dbs[$db_key] ?? $db_key]);
    }

    /**
     * 如果当前类的方法需要开启事物，只需要方法前加serv_即可（如果还有其他关联业务的，最好开一个service层）
     * @param  mix   $method
     * @param  array $args
     * @return mix
     */
    public static function __callStatic($method, $args)
    {
        return static::call_serv_static(static::class, $method, $args);
    }

    /**
     * 返回有效的查询参数
     * @param    array        $conds    参数说明可以看lists方法
     * @param    bool|boolean $conds_is_where conds是否为条件数组，如果为true,则认为conds为where
     * @return   array              
     */
    private static function _format_conds(array $conds, bool $conds_is_where = false)
    {
        if ( $conds_is_where && !isset($conds['where']) ) 
        {
            $where['where'] = $conds;
            $conds = $where;
        }

        return $conds;
    }

    /**
     * @param array
     * 下面的参数都是可选的
     * 'fields' => 字段, //默认为*
     * 'table'  => 表,   //默认去self::$table，支持子语句
     * join操作中 where和join中的字段，如果是主表的话，可以省略表名，jion的表的字段才需要加上表名
     * 'where' => 查询条件,
     * [
     *     //一般条件
     *     'uid' => 'abc',
     *     //如果需要使用类似in/like等关键字使用[in]
     *     'status[in]' => ...
     *     'xxx[like]' => ...
     *     'as_sql'    => 'alias' //如果要生成语句，1/bool 不带别名，字符串带别名
     * ]
     * 'join' => 联表操作
     * [    //table => on的条件
     *     //都没有写表名，field1会默认使用table1,field2会使用主表，即conds中的table
     *     'table1' => ['field1' => 'field2'] //需要别名，'table1 (as) xx' => ['field1' => 'field2']
     *      //field1会默认使用table1 field2不处理, 如果field3要等于某个值，使用expr
     *      //如果需要使用 left/right/inner等操作，只需要在表名后加[left]
     *     'table2[left]' => ['field1' => 'xxxx.field2', 'field3' => pub_mod_model::expr(22222)]
     *     .....
     * ]
     * alias    别名
     *
     * 支持子语句做jion的表
     *  $sql = mod_test::lists([
     *       'table'  => 'dx_stat_test',
     *       'as_sql' => 'bb',
     *   ]);
     *
     *   $arr = mod_test::lists([
     *       'join' => [$sql.'[left]' => ['game_id' => 'id']]
     *   ]);
     * is_master     是否查主库
     * lock          是否上独占锁   
     * share         共享锁
     * force_index   强制使用某个索引
     * filter_where  过滤掉为空的查询条件
     * 下面字段一般是列表才需要
     * count     是否返回汇总信息，为true的时候返回['data' => [], 'total' => xx]，一些大表可以传total进来，不会计算
     * as_sql    返回sql, as_sql为字符串的时候，返回带别名的sub sql
     * order_by  排序 [a => 'asc', 'b' => 'desc']
     * group_by  分组 'a' or ['a', 'b']
     * union     ['all' => $sql, $sql1, $sql2] // ['all' => [$sql1, $sql2], ...] 如果是union all用一个key all => string/array
     * is_union_table 如果为ture 不会把当前表当主表，而是把union中的语句当表使用，指定别名，只需要指定一个字符串
     * having    使用方法和where一样
     * index     以某个字段作为key返回
     * db_table  是否加上库名 true/false 开启后会自动加上数据库前缀，如果重载了_init，记得调用下父类的_init，否则可能导致失效
     * db_table=true 会根据当前类自动判断数据库，也可以指定数据库db_table='xxxdb'(一般不需要), 一些特殊的也可以直接调用self::db_table() 函数
     * page/offset／pagesize
     * next_page 对于app,一般不需要计算总数，只需要是否需要下一页，这个时候，这个参数设置为true就好了
     * formatter: //实际调用了基类的data_formatter，值可以为：
     * true    则默认为调用当前类的format_data
     * 字符串   先回尝试是否为可执行方法，否则就是当前类方法
     * 数组     [class, func]
     * 匿名函数  function($data) {
     *     foreach($data as &$v)
     *     {
     *         //格式化
     *     }
     *
     *     return $data;
     * }
     *
     * 大表数据缓存参数：true获取默认缓存时间(86400),如果需要自定义时间，直接传一个数字即可
     * cache_data   是否缓存数据
     * cache_total  自动获取数据列表和统计的时候，如果需要缓存统计数据的时候设置
     *
     * 需要分批查询，只需加上 batch_num 和 batch_field batch_num必须和where中需要分批的关键字对应
     * 参数都可为空
     * @param    bool|boolean $conds_is_where 如果为true,则认为conds为where
     * @return   mixed
     */
    final public static function lists(array $conds = [], bool $conds_is_where = false)
    {
        $data    = [];
        $conds   = static::_format_conds($conds, $conds_is_where);
        $columns = static::get_fields($conds);
        $table   = static::table($conds);
        $is_join = static::is_join($conds);
        // 检查是否有不允许的字段
        foreach($conds as $field => $field_val)
        {
            if ( !in_array($field, static::$func_allow_fields) && $field != static::$pk ) 
            {
                log::write(__function__ . '_unknow_field', static::class . ':' . $field);
                self::exception(
                    sprintf("Unknow field %s(%s)", $field, static::class), 
                    self::$unknow_err_status
                );
            }
            else if( $field == static::$pk )
            {
                $conds['where'][static::$pk] = $field_val;
            }
        }

        if( empty($table) )
        {
            return isset($conds['count']) ? ['data' => [], 'total' => 0] : [];
        }

        // 没有直接声明跨库 自动判断是否需要跨库
        if ( empty($conds['db_table']) && !empty($conds['join']) ) 
        {
            $load_dbs = [static::get_db_name($table)];
            foreach($conds['join'] as $tb => $relation)
            {
                $load_dbs[] = static::get_db_name($tb);
            }

            $load_dbs = array_unique($load_dbs);
            // 如果当前查询的数据库数量大于1，则需要跨库
            if ( count($load_dbs) > 1 ) 
            {
                $conds['db_table'] = true;
                $table = static::table($conds);
            }
        }

        $_table = $table . (!empty($conds['alias']) ? " {$conds['alias']}" : '');
        if ( is_object($table) ) 
        {
            $_table = self::expr($_table);
        }

        //table为子语句不进行解析
        if ( is_string($_table) && preg_match('#\(.*\)#', $_table)) 
        {
            $_table = self::expr($_table);
        }
        else if( static::$pk && is_string(static::$pk) && !empty($conds[static::$pk]) )
        {
            /**
             * 主要是获取主键信息，没有缓存或者之前没执行init_table的话会执行db_connection的reset()方法
             * 导致查询配置被初始化
             * 所以为了避免影响到当前的主要查询要在db::select之前执行下
             */
            static::init_table($table);
        }

        //过滤掉空条件，减少调用的判断
        if( !empty($conds['filter_where']) )
        {
            foreach($conds['where'] as $f => $w)
            {
                if( empty($w) && !is_numeric($w) )
                {
                    if(
                        is_array($conds['filter_where']) && in_array($f, $conds['filter_where']) ||
                        !is_array($conds['filter_where'])
                    )
                    {
                        unset($conds['where'][$f]);
                    }
                }
            }
        }

        $tmp_data = [];
        if ( !empty($conds['cache_data']) ) 
        {
            $cache_key = md5(serialize(array_diff_key(
                $conds, 
                ['cache_data' => null, 'cache_total' => null])
            ));
            $tmp_data  = cache::get($cache_key);
        }

        if ( !$tmp_data ) 
        {
            $query = static::from_module_db(db::select($columns))
                ->from($_table);
            //锁表只走主库，要不很容易悲剧
            if( !empty($conds['lock']) || !empty($conds['share']) )
            {
                //排他锁
                if( !empty($conds['lock']) )
                {
                    $query->lock();
                }
                //共享锁
                else if( !empty($conds['share']) )
                {
                    $query->share();
                }

                //强制走主库
                $conds['is_master'] = 1;
            }

            $alias = $is_join || !empty($conds['alias']) ? self::get_alias($conds) : '';
            //强制index
            !empty($conds['force_index']) && $query->force_index($conds['force_index']);
            !empty($is_join) && $query = self::_join($query, $conds);
            if( !empty($conds['id']) )
            {
                $query->where(static::get_column(static::get_pk($conds), $alias), $conds['id']);
            }
            //分析where条件
            else if( !empty($conds['where']) )
            {
                self::_where($query, $conds['where'], $alias);
            }

            //排序，支持数组和string
            if( !empty($conds['order_by']) )
            {
                $query = static::_order_by($query, $conds['order_by']);
            }

            //没有条件最大pagesize数量，防止手抖
            $pagesize = ($conds['pagesize'] ?? $conds['limit'] ?? null) ?? static::$pagesize;
            $limit    = $conds['limit'] ?? ($conds['pagesize'] ?? null);
            if ( 
                !isset($conds['as_sql'])  &&
                empty($conds['where'])   && 
                empty($conds['group_by']) && 
                empty($conds['page'])     && 
                empty($conds['limit']) 
            ) 
            {
                $conds['limit'] = $conds['pagesize'] ?? static::$max_pagesize;
            }

            //对于一些大表，因为innodb统计总数不像myisam那样本来已经统计好了，
            //所以会非常慢，一般不做分页，只显示是否有下一页
            if ( !empty($conds['next_page']) )
            {
                $_pagesize = $pagesize;
                ++$pagesize;
            }

            //分页显示数据
            if( isset($conds['page']) || isset($conds['offset']) )
            {
                $page = max(1, (isset($conds['page']) ? $conds['page'] : 1));
                $offset = !empty($conds['offset']) ?
                intval($conds['offset']) : $pagesize*($page-1);

                if ( !empty($conds['next_page']) )
                {
                    $offset -= ($page - 1);
                }
                $query->limit($pagesize)->offset($offset);
            }
            //返回N条
            else if( $limit )
            {
                $query->limit($limit);
            }

            //clickhouse专有函数
            if( !empty($conds['limit_by']) )
            {
                $query->limit_by($conds['limit_by']);
            }

            //分组
            if( !empty($conds['group_by']) )
            {
                $query->group_by($conds['group_by']);
            }

            //使用聚合计算出来的字段来过滤
            if( !empty($conds['having']) )
            {
                self::_having($query, $conds['having']);
            }

            if ( !empty($conds['union']) ) 
            {
                self::_union($query, $conds['union']);
                if ( isset($conds['is_union_table']) ) 
                {
                    $query->is_union_table($conds['is_union_table']);
                }
            }

            // 分批查询
            if ( 
                !empty($conds['batch_num']) && !empty($conds['batch_field']) &&
                !empty($conds['where'][$conds['batch_field']])
            ) 
            {
                $data             = [];
                $batch_field_vals = array_unique((array) $conds['where'][$conds['batch_field']]);
                $batch_field_arr  = array_chunk($batch_field_vals, $conds['batch_num']);
                foreach($batch_field_arr as $tmp)
                {
                    $query->reset();
                    $new_conds = $conds;
                    $new_conds['where'][$conds['batch_field']] = $tmp;
                    
                    unset($new_conds['batch_field'], $new_conds['batch_num']);
                    $data = array_merge(
                        $data, 
                        (array) static::lists($new_conds, $conds_is_where)
                    );
                }

                return $data;
            }

            //返回sql
            if ( isset($conds['as_sql']) ) 
            {
                $sql = $query->get_compiled_sql();
                //生成带alias的自查询
                if ( is_string($conds['as_sql']) ) 
                {
                    $sql = sprintf('(%s) %s', $sql, $conds['as_sql']);
                }

                return $sql;
            }

            if ( !empty($conds['db_row_fn']) ) 
            {
                $query->set_row_fn($conds['db_row_fn']);
            }

            $is_master = empty($conds['is_master']) ? false : true;
            $tmp_data  = (array) $query->execute($is_master);
            isset($cache_key) && $tmp_data && cache::set(
                $cache_key, 
                $tmp_data, 
                is_numeric($conds['cache_data']) ? $conds['cache_data'] : static::$cache_time
            );
        }

        $index = -1;
        $stack = [];
        //指定返回数组的key
        foreach($tmp_data as $row)
        {
            if ( !empty($conds['index']) && is_array($conds['index']) ) 
            {
                $index = [];
                foreach($conds['index'] as $f)
                {
                    $index[] = $row[$f] ?? null;
                }

                $index = implode(":", $index);
            }
            else if( !empty($conds['index']) && isset($row[$conds['index']]) )
            {
                $index = $row[$conds['index']];
            }
            else
            {
                ++$index;
            }

            $stack[$index] = $row;
        }

        //是否有下一页，一般用作app的下一页
        if ( !empty($conds['next_page']) )
        {
            $has_next_page = false;
            if ( count($stack) > $_pagesize )
            {
                $has_next_page = true;
                array_pop($stack);
            }

            $data = [
                'data'      => $stack,
                'next_page' => $has_next_page, 
                'prev_page' => !empty($conds['page']) && $conds['page'] > 1,
            ];
        }
        //是否汇总分页
        else if( !empty($conds['count']) )
        {
            $data = [
                'data'  => $stack,
                //有些大表需要缓存总数的，可以传进来，如果没有数据则不需要查询总数
                'total' => $conds['total'] ?? (!$stack ? 0 : static::count(array_merge(
                    $conds, 
                    [
                        'orgin_fields' => $conds['fields'] ?? null,
                        'fields'       => $conds['count_fields'] ?? '*', 
                        'formatter'    => null,
                        'offset'       => null,
                        'cache_data'   => $conds['cache_total'] ?? null,
                    ]
                ))),
            ];
        }
        else
        {
            $data = $stack;
        }
        
        return (array) static::data_formatter($data, $conds['formatter'] ?? null);
    }

    /**
     * 获取一条数据，参数和lists完全一致
     * @param    array        $conds          和lists一致
     * @param    bool|boolean $conds_is_where bool|boolean $conds_is_where 如果为true,则认为conds为where
     * @return   mixed
     */
    final public static function find(array $conds, bool $conds_is_where = false)
    {
        $conds = static::_format_conds($conds, $conds_is_where);
        $data  = (array) self::lists(array_merge($conds, [
            'limit' => 1,    //强制获取一条
            'page'  => null, //会影响取单条数据
            'count' => null, //会影响取单条数据
        ]));

        return $data ? current($data) : $data;
    }

    /**
     * 根据主键获取数据
     * @param    mixed     $pk_val 单个id/数组
     * @param    bool      $lock   是否上锁
     * @param    string    $field  默认 *
     * @return   array     pk_val为数组，返回二维 否则返回一维           
     */
    final public static function info($pk_val, ?bool $lock = false, $fields = '*'):array
    {
        if ( $pk = self::get_pk() ) 
        {
            $data = self::lists([
                'where'  => [$pk => $pk_val],
                'fields' => $fields,
                'index'  => $pk,
                'lock'   => $lock,
            ]);

            $data && $data = is_array($pk_val) ? $data : reset($data);
        }

        return $data ?? [];
    }

    /**
     * 插入单条/多条数据
     * @param    array      $data   添加的数组
     * @param    mixed      $table  表名一般不需要，会用默认表名(如果是数组，可以设置table/ignore/delay/dups等)
     * @param    boolean    $ignore 是否忽略错误
     * @param    boolean    $delay  是否延时更新，操作业务禁止使用，一般是不太重要的数据可以使用
     * @return   array            
     */
    final public static function insert(
        array $data, 
        $table       = '', 
        bool $ignore = false, 
        bool $delay  = false
    )
    {
        if( empty($data) )
        {
            return false;
        }
  
        $extr  = []; //扩展属性
        //如果table为数组有可能带有其他参数
        if( is_array($table) )
        {
            $extr   = $table;
            $table  = isset($extr['table']) ? $extr['table'] : static::$table;
            $ignore = isset($extr['ignore']) ? $extr['ignore'] : $ignore;
            $delay  = isset($extr['delay']) ? $extr['delay'] : $delay;
        }

        // 批量分批插入
        if ( !empty($extr['batch_num']) && $extr['batch_num'] > 1 ) 
        {
            $muti_data = array_chunk($data, $extr['batch_num']);
            unset($extr['batch_num']);

            $suc_num = 0;
            foreach($muti_data as $data)
            {
                list($last_id, $status) = self::insert($data, $extr, $ignore, $delay);
                $suc_num += $status;
                if ( !empty($extr['usleep_time']) ) 
                {
                    usleep($extr['usleep_time']);
                }
            }

            return [$last_id, $suc_num];
        }

        $keys     = array_keys($data);
        //判断是否为批量插入
        $mutipule = is_array(reset($data)) && is_numeric(reset($keys)) ? true : false;
        $table    = empty($table) ? static::$table : $table;
        if( empty($table) ) return false;
     
        $query = static::from_module_db(db::insert($table))
            ->ignore($ignore)
            ->delay($delay);

        if( !empty($mutipule) ) //批量插入
        {
            $query->values($data)->columns(array_keys(current($data)));
        }
        else //单条插入
        {
            $query->set($data);
        }

        //批量更新（遇到重复主键更新，否则插入）
        if( !empty($extr['dups']) )
        {
            $query->dup($extr['dups']);
        }

        return $query->execute();
    }

    /**
     * 更新数据
     * @param    array      $data     需要更新的数组
     * @param    array      $where    更新条件(如果设置了_cmp_field_ = $pk，则表示进行批量更新)
     * @param    mixed      $table    一般不需要写，(如果是数组，可以设置table/ignore/delay等)
     * @param    boolean    $ignore   是否忽略错误
     * @param    boolean    $delay    是否延时更新，操作业务禁止使用，一般是不太重要的数据可以使用
     * @return   int          
     */
    final public static function update(
        array $data, 
        array $where, 
        $table       = '', 
        bool $ignore = false, 
        bool $delay  = false
    )
    {
        if( empty($data) || empty($where) )
        {
            return false;
        }
        //是否为批量更新
        else if( !empty($where['_cmp_field_']) )
        {
            $is_batch = array_column($data, $where['_cmp_field_']);
            if ( !$is_batch || ($first = reset($data)) && !is_array($first) ) 
            {
                return false;
            }
        }

        $table = empty($table) ? static::$table : $table;
        //如果table为数组有可能带有其他参数
        if( is_array($table) )
        {
            $tmp    = $table;
            $table  = isset($tmp['table']) ? $tmp['table'] : static::$table;
            $ignore = isset($tmp['ignore']) ? $tmp['ignore'] : $ignore;
            $delay  = isset($tmp['delay']) ? $tmp['delay'] : $delay;
            $limit  = $tmp['limit'] ?? null;
            unset($tmp);
        }

        if( empty($table) || !$data ) return false;
        $query = static::from_module_db(db::update($table))
            ->set($data)
            ->ignore($ignore)
            ->delay($delay);

        !empty($limit) && $query->limit($limit);
        $result = false;
        //批量更新
        if ( !empty($is_batch) ) 
        {
            $query = $query->set_update_cmp_field($where['_cmp_field_']);
            unset($where['_cmp_field_']);
        }
        
        if ( !empty($where) ) 
        {
            self::_where($query, $where);
        }
 
        //强制带where或者批量更新才可以执行
        if ( !empty($is_batch) || false != db::has_where() )
        {
            $result = $query->execute();
        }

        return $result;
    }


    /**
     * 删除数据
     * @param    array      $where    删除条件
     * @param    string     $table    表名，默认self::$table
     * @param    ?int       $limit    指定删除行数
     * @param    mixed      $order_by 排序
     * @param    boolean    $ignore   是否忽略错误
     * @return   int         
     */
    final public static function delete(
        array   $where, 
        ?string $table    = null, 
        ?int    $limit    = null, 
                $order_by = null,
        bool    $ignore   = false
    )
    {
        $table = !$table ? static::$table : $table;
        if( !$table || !$where ) return false;

        $result = false;
        $query  = static::from_module_db(db::delete($table))->ignore($ignore);

        static::_where($query, $where);
        $limit    && $query->limit($limit);
        $order_by && static::_order_by($query, $order_by);

        //强制带where
        if( false != db::has_where() )
        {
            $result = $query->execute();
        }

        return $result;
    }

    /**
     * 根据主键删除数据
     * @param    mixed     $pk_val 单个id/数组
     * @return   bool    
     */
    final public static function del_info($pk_val, $filter_type = 'text'):bool
    {
        if ( $pk = self::get_pk() ) 
        {
            return self::delete([$pk => cls_filter::filter($pk_val, $filter_type)]);
        }

        return false;
    }

    /**
     * 求总数，参数和lists是一样的，可以为空
     * @param    array        $conds          和lists的一样
     * @param    bool|boolean $conds_is_where 如果为true,则认为conds为where
     * @return   int
     */
    final public static function count(array $conds = [], bool $conds_is_where = false)
    {
        $conds = static::_format_conds($conds, $conds_is_where);
        if( empty($conds['fields']) )
        {
            $table = empty($conds['table']) ? '' : $conds['table'];
            //self::init_table($table);
            $conds['fields'] = '*';
            if ( true == self::is_join($conds) && static::$pk )
            {
                $conds['fields'] =  static::get_alias($conds).'.'.static::$pk;
            }
        }

        $fields = [];
        $func   = $conds['func'] ?? 'COUNT';
        $tmp    = is_array($conds['fields']) ? $conds['fields'] : explode(',', $conds['fields']);
        foreach($tmp as $f)
        {
            $alias = $f;
            if ( preg_match('#(?<table>[^\s]+\.)?(?<field>[^\s]+)(\s+(as)?\s*(?<alias>[^\s]+))?#i', $f, $mat) ) 
            {
                $alias = $mat['alias'] ?? $mat['field'];
                //group by 会自动加一个字查询，所以不能带表名
                if ( isset($conds['group_by']) ) 
                {
                    $f = $mat['field'];
                }
                else if ( !empty($mat['alias']) ) 
                {
                    $f = ($mat['table'] ?? '') . $mat['field'];
                }
            }

            $fields[] = sprintf('%s(%s) AS %s', $func, $f, $f == '*' ? 'total' : $alias);
        }

        $orgin_fields    = $conds['orgin_fields'] ?? null;
        unset($conds['order_by'], $conds['orgin_fields']);
        // 求大约值
        $is_nearly_count = empty($conds['where']) && !empty($conds['nearly_count']);
        //有group by的统计要先加一个子查询
        if ( isset($conds['group_by']) || isset($conds['union']) ) 
        {
            $sub_sql = static::lists(array_merge($conds, [
                'fields'   => $orgin_fields ?? $conds['fields'],
                'as_sql'   => 'sub_sql',
                'page'     => null,
                'pagesize' => null,
                'limit'    => null,
            ])) ?: [];

            $ret = static::find([
                'table'  => static::expr($sub_sql),
                'fields' => $fields,
                'func'   => $conds['func'] ?? null,
                'count'  => true,
                'as_sql' => $is_nearly_count ?: null,
            ]) ?: [];

            // 获取表近似值
            if ( $is_nearly_count ) 
            {
                return static::get_nearly_count($ret, $conds['is_master'] ?? null);
            }
        }
        else
        {
            $ret = static::find(array_merge($conds, [
                'fields' => is_object($conds['fields']) ? $conds['fields'] : $fields,
                'count'  => true,
                'as_sql' => $is_nearly_count ?: null,
            ])) ?: [];
            // 获取表近似值
            if ( $is_nearly_count ) 
            {
                return static::get_nearly_count($ret, $conds['is_master'] ?? null);
            }
        }

        return (1 == count($fields) || is_object($conds['fields'])) ? reset($ret) : $ret;
    }

    /**
     * 获取表近似值
     * @param    string       $sql      
     * @param    bool|boolean $is_master
     * @return   int             
     */
    final public static function get_nearly_count(string $sql, ?bool $is_master = false):int
    {
        $tmp = static::query('EXPLAIN ' . $sql, $is_master);
        return $tmp[0]['rows'] ?? 0;
    }

    /**
     * 求总和，参数和lists是一样的，可以为空
     * @param    array      $conds
     * @param    bool|boolean $conds_is_where 如果为true,则认为conds为where
     * @return   mixed           
     */
    final public static function sum(array $conds, bool $conds_is_where = false)
    {
        return static::count(array_merge($conds, ['func' => 'SUM']), $conds_is_where);
    }

    /**
     * 求平均值，参数和lists是一样的，可以为空
     * @param    array      $conds
     * @param    bool|boolean $conds_is_where 如果为true,则认为conds为where
     * @return   mixed           
     */
    final public static function avg(array $conds = [], bool $conds_is_where = false)
    {
        return static::count(array_merge($conds, ['func' => 'AVG']), $conds_is_where);
    }

    /**
     * @param    array        $conds           参数和lists一样
     * @param    bool|boolean $conds_is_where  如果为true,则认为conds为where
     * @return   mixed                         返回fields中的第一个字段的值
     */
    final public static function field(array $conds, bool $conds_is_where = false)
    {
        $data = static::find($conds, $conds_is_where);
        return $data ? reset($data) : null;
    }

    /**
     * 使用模块数据库，实际是对db的from_db函数的封装 和 db::selet(xxx)->from_db('实例名称')效果一样
     * @param  object $query 当前查询
     * @return $query
     */
    final public static function from_module_db($query)
    {
        if ( static::$module_db && !static::$_module_dbs ) 
        {
            static::_init();
        }

        if( is_object($query) )
        {
            static::_load_module_env();
            $module_db = static::get_current_db_config();
            $query->from_db(
                $module_db['name'] ?? null,
                $module_db['config_file'] ?? null,
                !empty($module_db['set_default_db']),
                $module_db['slave_index'] ?? null
            );
        }

        return $query;
    }

    /**
     * 获取查询字段
     * 1.不传或者*返回主表的全部字段
     * 2.不联表的情况下 可以使用 ['fields' => 'a,b,c']/['fields' => [a, b, c]]
     * 3.如果除了查主表还要查询联表的字段的话
     * ['table1' => [a, b, c], 'table2' => 'd,e,f']
     * @param  上方法提交的数组
     * @return string
     * 返回查询字段
     */
    final public static function get_fields(array $conds)
    {
        static $database_config = [];
        $table  = self::get_alias($conds);
        $fields = $conds['fields'] = $conds['fields'] ?? '*';
        if( empty($conds['fields']) || $conds['fields'] === '*' )
        {
            if( !empty($table) && is_string($table) )
            {
                $table = db::table_prefix($table);
                if( empty($database_config) )
                {
                    $database_config = config::instance('database')->get();
                }

                //如果没有加密字段的表，fields为*，降低io
                if( isset($database_config['crypt_fields'][$table]) )
                {
                    self::init_table($table);
                    if( !empty(static::$exclude_fields) )
                    {
                        foreach(static::$exclude_fields as $f)
                        {
                            unset(static::$table_ifnos['fields'][$f]);
                        }
                    }

                    $fields = array_keys(static::$table_ifnos['fields']);
                }
                else
                {
                    $fields = ['*'];
                }
            }
            else if( !empty(static::$fields) )
            {
                $fields = static::$fields;
            }
        }
        //联表分别指定字段 'fields' => ['table1' => ['a', 'b'], 'tableb' => 'c,d']
        else if( is_array($conds['fields']) && !isset($conds['fields'][0]) )
        {
            $fields = [];
            foreach($conds['fields'] as $tb => $item)
            {
                $tb  = empty($tb) ? $table : $tb;
                $row = is_array($item) ? $item : array_map('trim', explode(',', $item));
                foreach($row as $f)
                {
                    $f = strpos($f, '.') > 0 ? $f : $tb .'.' .$f;
                    $fields[$f] = $f;
                }
            }
        }
        else
        {
            $fields = $conds['fields'];
        }

        //联表图特殊处理
        if( true == self::is_join($conds) && empty($conds['alias']) )
        {
            $conds['alias'] = static::get_alias($conds);
        }

        //非对象的，转成数组
        if ( !is_object($fields) && !is_array($fields) )
        {
            $fields = array_map('trim', explode(',', $fields));
        }

        $has_object = false;
        if( is_array($fields) )
        {
            foreach($fields as $k => $f)
            {
                if ( preg_match('#\(.*\)#', $f) ) 
                {
                    $f = self::expr($f);
                }

                if ( is_object($f) )
                {
                    $fields[$k]  = $f;
                    $has_object = true;
                }
                else if(
                    $f != '*' && 
                    !strstr($f, '(') &&
                    !empty($conds['alias']) && false === strstr($f, '.') 
                )
                {
                    $f = trim($f);
                    $fields[$k] = $conds['alias'].'.'.$f;
                }
            }
        }

        //fields存在对象需要返回数组
        if ( $has_object )
        {
            return $fields;
        }
        else
        {
            return is_array($fields) ? implode(',', $fields) : $fields;
        }
    }

    /**
     * 判断是否联表
     * @param    array      $conds
     * @return   boolean          
     */
    final protected static function is_join(array $conds)
    {
        return !empty($conds['join']);
    }

    /**
     * @param  上层方法提交的数组
     * @return string
     * 返回查询字段
     */
    final public static function get_pk(?array $conds = [])
    {
        $pk = '';
        $table = empty($conds['table']) ? static::$table : $conds['table'];
        if ( static::$pk ) 
        {
            $pk = static::$pk;
        }
        else if( !empty($conds['pk']) )
        {
            $pk = $conds['pk'];
        }
        else if( !empty($table) )
        {
            self::init_table($table);
            $pk = static::$table_ifnos['pk'] ?? '';
        }

        return $pk;
    }


    /**
     * @param  上层方法提交的数组
     * @return string
     * 返回查询字段
     */
    final protected static function table($conds = [])
    {
        $table = '';
        if( is_array($conds) && !empty($conds['table']) )
        {
            $table = $conds['table'];
        }
        else if( !empty(static::$table) )
        {
            $table = static::$table;
        }
        else
        {
            $table = static::$table_ifnos['table'] ?? null;
        }

        // 加上库名
        if ( $table && !empty($conds['db_table']) ) 
        {
            $table = static::db_table(
                $table, 
                is_string($conds['db_table']) ? $conds['db_table'] : null
            );
        }

        return $table;
    }

    /**
     * 获取表别名
     * @param    array     $table
     * @return   string       
     */
    final public static function get_alias($table)
    {
        if ( is_array($table) ) 
        {
            $table = $table['alias'] ?? static::table($table);
        }

        if ( is_string($table) && preg_match('#\([^\(\)]+\)\s(?<alias>[^\s]+)#', $table, $mat) ) 
        {
            $table = $mat['alias'];
        }
        else if( 
            is_string($table) && 
            !preg_match('#\([^\(\)]+\)#', $table, $mat) &&
            preg_match('#[\w]\s+(as\s+)?(?<alias>[^\s]+)#i', $table, $mat)
        )
        {
            $table = $mat['alias'];
        }

        return $table;
    }

    /**
     * @param    string     $column
     * @param    ?string    $alias 
     * @return   string      
     */
    final public static function get_column(string $column, $alias = null)
    {
        return ($alias && !stristr($column, '.') ? $alias . '.' : '') . $column;
    }

    /**
     * @param  表
     * @return 表结构数据
     */
    final public static function init_table($table = null)
    {
        static $table_ifnos = array();
        $table = empty($table) ? static::$table : $table;
        if( empty($table) )
        {
            return array();
        }
        else if( isset($table_ifnos[$table]) )
        {
            static::$table_ifnos = $table_ifnos[$table];
            return $table_ifnos[$table];
        }

        static $table_cache_time = null;
        if ( !isset($table_cache_time) ) 
        {
            $table_cache_time = config::instance('config', 'db')->get('table_cache_time', 0);
        }

        $cache_key = md5(static::MD_KEY . '_' . $table . '_' . $table_cache_time);
        static::$table_ifnos = cache::get($cache_key);
        if( empty(static::$table_ifnos) || !is_array(static::$table_ifnos) )
        {
            static::$table_ifnos          = [];
            static::$table_ifnos['table'] = $table;
            $sql   = "SHOW FULL FIELDS FROM `" . $table . "`";
            $query = static::query($sql, false);
            foreach($query as $row)
            {
                if( $row['Key'] === 'PRI' && empty(static::$table_ifnos['pk']) )
                {
                    static::$table_ifnos['pk'] = $row['Field'];
                }

                unset($row['Privileges'], $row['Collation'], $row['Extra']);
                static::$table_ifnos['fields'][$row['Field']] = $row;
            }

            util::shutdown_function(
                array('kaliphp\cache', 'set'),
                array($cache_key, static::$table_ifnos, static::$cache_time)
            );

            $table_ifnos[$table] = static::$table_ifnos;
        }

        return static::$table_ifnos;
    }

    /**
     * 获取当前加载的表，所在的库
     * @param    string     $table
     * @return   ?string       
     */
    final public static function get_db_name(?string $table = null)
    {
        if ( 
            $table &&
            isset(static::$_load_tables[$table]) && 
            count(static::$_load_tables[$table]) == 1 
        ) 
        {
            $dbs     = array_keys(static::$_load_tables[$table]);
            $db_name = reset($dbs);
        }
        else
        {
            $db_name = static::$_module_dbs[static::class]['name'] ?? 
            (static::$module_db['name'] ?? null);
        }

        return $db_name ?? null;
    }

    /**
     * 获取是否是要加上database
     * @param    ?string     $table    为空会取当前model的table
     * @param    ?string     $db_name  一般不需要填写，会自动获取
     * @return   string 
     */
    final public static function db_table(?string $table = null, ?string $db_name = null)
    {
        $table    = $table ?? static::$table;
        $db_name  = $db_name ?: self::get_db_name($table);
        $database = db::get_config($db_name, 'name');
        if ( !strstr($table, '.') ) 
        {
            $table = sprintf('`%s`.`%s`', $database, $table);
        }

        return $table;
    }

    /**
     * 联表操
     * 'join' => 联表操作
     * [    //table => on的条件
     *     //都没有写表名，field1会默认使用table1,field2会使用主表，即conds中的table
     *     'table1' => ['field1' => 'field2'],需要别名，'table1 (as) xx' => ['field1' => 'field2']
     *      //field1会默认使用table1 field2不处理, 如果field3要等于某个值，使用expr
     *      //如果需要使用 left/right/inner等操作，只需要在表名后加[left]
     *     'table2[left]' => ['field1' => 'xxxx.field2', 'field3' => pub_mod_model::expr(22222)]
     *     .....
     * ]
     * @param  object $query
     * @param  array  $conds
     * @return object
     */
    final protected static function _join($query, $conds = [])
    {
        if( true == self::is_join($conds) )
        {
            $joins      = $tmp_table_maps = [];
            $main_table = static::get_alias($conds);
            foreach($conds['join'] as $table => $relation)
            {
                $pattern_sub = $alias = $tmp_table = null;
                //先把子语句替换成一个随机的字符串，后面再替换回来
                if ( preg_match('#(?<sub_sql>\(.*\))#is', $table, $mat) ) 
                {
                    $tmp_table   = uniqid();
                    $tmp_table_maps[$tmp_table] = $mat['sub_sql'];
                    $table       = str_replace($mat['sub_sql'], $tmp_table, $table);
                    $pattern_sub = '#(?<table>[^\[\]\s]+)(\s+(as\s+)?\s*(?<alias>[^\s\[\]]+))?(\[(?<join_type>[^\[\]]+)\])#is';
                }

                $join_type = '';
                //匹配出连表方式和别名
                $pattern   = '#(?<table>[^\[\]\s]+)(\s*\[(?<join_type>[^\[\]]+)\])?(\s+(as\s+)?\s*(?<alias>[^\s\[\]]+))?#is';
                if( 
                    (isset($pattern_sub) && preg_match($pattern_sub, $table, $mat)) || 
                    preg_match($pattern, $table, $mat) 
                )
                {
                    $table     = $mat['table'];
                    $join_type = $mat['join_type'] ?? null;
                    $alias     = $mat['alias'] ?? null;
                }

                // 非临时表判断是否加上库名
                if ( !isset($tmp_table) ) 
                {
                    $table = static::table(array_merge($conds, ['table' => $table]));
                }
                
                $joins[$table] = [
                    'tb'    => $table,
                    'type'  => $join_type,
                    'data'  => [],
                    'alias' => $alias ?? null,
                ];

                $alias = $alias ?? $table;
                foreach ($relation as $key => $value)
                {
                    list($key, $operator, $value) = array_values(static::parse_where_item($query, $key, $value, '', ''));
                    $joins[$table]['data'][] = [
                        (
                            is_string($key) && strpos($key, '.') > 0 || is_object($key) ?
                                // For ['tableB.column' => 'column']
                                $key :

                                // For ['column1' => 'column2']
                                $alias . '.`' . $key . '`'
                        ) ,
                        (
                            is_string($value) && strpos($value, '.') > 0 || is_object($value) ?
                            $value :
                            (is_string($value) ? $main_table . '.`' . $value . '`' : $value)
                        ),
                        $operator
                    ];
                }

                if (empty($joins[$table]['data']) ) unset($joins[$table]);
            }

            foreach($joins as $table => $config)
            {
                if ( isset($tmp_table_maps[$table]) ) 
                {
                    $table = $tmp_table_maps[$table];
                }

                if ( isset($config['alias']) ) 
                {
                    $table = [$table, $config['alias']];
                }

                $query->join($table, $config['type']);
                foreach($config['data'] as $row)
                {
                    $query->on($row[0], $row[2], $row[1]);
                }
            }
        }

        return $query;
    }

    /**
     * union 操作
     * @param    object     $query
     * @param    mixed     $union
     * @return   object      
     */
    private static function _union($query, $union, ?string $type = null)
    {
        if ( is_string($union) ) 
        {
            return $query->union($union, $type);
        }

        foreach($union as $k => $u)
        {
            if ( strtolower($k) === 'all' || $k === '' ) 
            {
                static::_union($query, $u, $k);
            }
            else
            {
                static::_union($query, $u, $type);
            }
        }

        return $query;
    }

    /**
     * 解析where里面的操作符
     * //['a[in]' => 'xxx', 'b[like]' => 'acb']
     * @param  object $query 
     * @param  string $column
     * @param  mixed  $value 
     * @param  string $table 
     * @param  string $func  
     * @return void     
     */
    final public static function parse_where_item($query, $column, $value, $table = '', $func = 'where')
    {
        if( preg_match('#(?<field>[^\[\]]+)\[(?<op>[^\[\]]+)\]#is', $column, $mat) )
        {
            $field     = (false === strstr($mat['field'], '.') && $table ? $table .'.' : '' ).$mat['field'];
            $mat['op'] = strtoupper($mat['op']);
            switch ($mat['op']) 
            {
                case '%$%': case '%': //全模糊
                case '%$':  //左模糊
                case '$%':  //右模糊
                    $value = static::_parse_like_where_item($query, $column, $value, $mat['op'], $table);
                    if ( is_string($value)  ) 
                    {
                        $op = 'LIKE';
                    }
                    else
                    {
                        return $query;
                    }

                    break;
                case 'BETWEEN':
                    $first = reset($value);
                    $last  = end($value);
                    $first = is_object($first) ? $first : "'{$first}'";
                    $last  = is_object($last) ? $last : "'{$last}'";
                    return $func ? $query->$func(self::expr(sprintf(
                        "%s BETWEEN %s AND %s", 
                        $field, 
                        $first, 
                        $last
                    ))) : [
                        'field' => $field,
                        'op'    => 'BETWEEN',
                        'value' => self::expr(sprintf(" %s AND %s ", $first, $last))
                    ];
                    break;
                case '><': // > xx < xx
                    if ( $func ) 
                    {
                        $value = (array) $value;
                        $query->$func($field, '>', reset($value));
                        return $query->$func($field, '<', end($value));
                    }
                    else
                    {
                        return [
                            'field' => self::expr(sprintf(" %s > '%s' ", $field, reset($value))),
                            'op'    => 'AND',
                            'value' => self::expr(sprintf(" %s < '%s' ", $field, end($value)))
                        ];
                    }
     
                    break;
                default:
                    //用&把多个操作符连起来，表示2个操作符
                    if (preg_match('#(?<op1>[^&]+)&(?<op2>[^&]+)#', $mat['op'], $sub_mat) )
                    {
                        if ( !$func ) 
                        {
                            return [
                                'field' => self::expr(sprintf(" %s %s '%s' ", $field, $sub_mat['op1'], reset($value))),
                                'op'    => 'AND',
                                'value' => self::expr(sprintf(" %s %s '%s' ", $field, $sub_mat['op2'], end($value)))
                            ];
                        }

                        $value = (array) $value;
                        $query->$func($field, $sub_mat['op1'], reset($value));
                        return $query->$func($field, $sub_mat['op2'], end($value));
                        break;
                    }

                    $op = $mat['op'];
                    break;
            }
        }
        else
        {
            $op    = is_array($value) ? 'IN' : '=';
            $field = (!is_object($value) && false === strstr($column, '.') && $table ? $table.'.' : '' ).$column;
        }

        if ( $func ) 
        {
            return $query->$func($field, $op, $value);
        }
        else
        {
            $no_func_opeater = !$func && !empty($mat['op']) && !is_object($value);
            return [
                'field' => $field,
                'op'    => $op,
                'value' => $no_func_opeater ? self::expr(sprintf("'%s'", $value)) : $value,
            ];
        }
    }

    /**
     * 获取like中的value,支持单个/数组
     * @param    object     $query 
     * @param    string     $column
     * @param    mixed      $value 
     * @param    string     $op    
     * @param    string     $table 
     * @return   mixed            
     */
    private static function _parse_like_where_item($query, $column, $value, $op, $table = '')
    {
        $format_rules = [
            '%$%' => '%[value]%',
            '%'   => '%[value]%',
            '%$'  => '%[value]',
            '$%'  => '[value]%'
        ];

        if ( is_array($value) ) 
        {
            $query->or_where_open();
            foreach ($value as $v) 
            {
                static::parse_where_item($query, $column, $v, $table, 'or_where');
            }

            $query->or_where_close();
            return $query;
        }
        else
        {
            // sprintf在多个%号有bug
            return str_replace('[value]', $value, $format_rules[$op]);
        }
    }

    /**
     * 用于where/having操作
     * 优先处理or/and，支持各种嵌套
     * or  会把条件用or连接起来
     * and 会把条件用and连接起来
     * 比如：
     * where'  => [
     *   'game_id[>]' => 1, 
     *   'game_id[>=]' => 2, 
     *   'or' => [
     *       'game_id[>]' => 3, 
     *       'game_id[!=]' => 4,
     *       'and' => [
     *           'game_id[>]' => 5, 
     *           'or'  => ['game_id[>]' => 6, 'game_id[<]' => 7],
     *           'and' => ['hour' => 1, 'or' => ['hour' => 1, 'hour[>]' => 10]]
     *       ] 
     *   ]
     *],
     *
     * 字段支持操作符：
     *  * %$% => like '%xx%'
     * %$  => like '%xx'
     * $%  => like 'xx%'
     * >< 并且值为长度为2的枚举  => 'a' > arr[0] and 'a' < $arr[1]
     * >=<= 并且值为长度为2的枚举  => 'a' >= arr[0] and 'a' <= $arr[1]
     * !=  => 不等于
     * in  => in (xx1, xx2) 这个一般不需要，只要值为数组，就会自动用in
     * @param    object     $query
     * @param    mixed     $where
     * @param    string     $table
     * @return   object         
     */
    final protected static function _where(object $query, $where, $table = '', $mod_func = 'where')
    {
        if( !$where )
        {
            return $query;
        }

        $func_arr = [
            'or'    => 'or_' . $mod_func,
            'and'   => 'and_' . $mod_func,
            'open'  => $mod_func . '_open',
            'close' => $mod_func . '_close',
        ];

        $table = self::get_alias($table);
        if ( is_object($where) )
        {
            return $query->$mod_func($where);
        }

        //优先处理or 和 and 关键字
        foreach (['or' => $func_arr['or'], 'and' => $func_arr['and']] as $or_and => $or_and_func)
        {
            if ( !isset($where[$or_and]) ) continue;
            $_first_item  = current($where[$or_and]);
            //是否是多个or查询
            $is_multi_or = (
                (is_array($_first_item) && is_string(current(array_keys($_first_item)))) ||
                (is_array($_first_item) && is_array(current($_first_item))) //兼容多维数组写法
            );

            $where[$or_and] = $is_multi_or ? $where[$or_and] : [$where[$or_and]];
            $query->{$func_arr['open']}();
            foreach ($where[$or_and] as $or_item)
            {
                if ( is_object($or_item) ) 
                {
                    $query->$mod_func($or_item);
                }
                else
                {
                    foreach ($or_item as $column =>  $value)
                    {
                        if (  in_array($column, ['or', 'and']) ) 
                        {
                            call_user_func([$query, $or_and_func . '_open']);
                            self::_where($query, [$column => $value], $table, $mod_func);
                            call_user_func([$query, $or_and_func . '_close']);
                            continue;
                        }

                        static::parse_where_item($query, $column, $value, $table, $or_and_func);
                    }   
                }
            }
            $query->{$func_arr['close']}();
            unset($where[$or_and]);
        }

        // exists/not_exist 关键字
        if ( isset($where['exists']) || isset($where['not_exist']) ) 
        {
            $exists_maps = [
                'exists'    => 'EXISTS',
                'not_exist' => 'NOT EXISTS',
            ];
            foreach($exists_maps as $k => $v)
            {
                if ( isset($where[$k]) ) 
                {
                    $where[$k] = (array) $where[$k];
                    foreach($where[$k] as $exists_sql)
                    {
                        if ( !preg_match('#^\(.*\)$#', $exists_sql) ) 
                        {
                            $exists_sql = sprintf('(%s)', $exists_sql);
                        }
                        
                        $query->$mod_func(self::expr(sprintf('%s %s', $exists_maps[$k], $exists_sql)));
                    }
                    unset($where[$k]);
                }
            }
        }

        foreach($where as $column => $value)
        {
            if ( is_numeric($column) )
            {
                //条件是对象。
                if ( !is_array($value) && is_object($value) )
                {
                    $query->$mod_func($value);
                    continue;
                }
                else if ( count($value) >= 2 ) 
                {
                    $field = (false === strstr($value[0], '.') && $table ? $table . '.' : '').$value[0];
                    if ( count($value) == 2 )
                    {
                        $query->$mod_func($field, $value[1]);
                    }
                    else
                    {
                        $query->$mod_func($field, $value[1], $value[2]);
                    }
                }
                else
                {
                    static::_where($query, $value, $table, $mod_func);
                }
            }
            else
            {
                PARESE_WHERE_ITEM:
                static::parse_where_item($query, $column, $value, $table, $mod_func);
            }
        }

        return $query;
    }

    /**
     * 把数组的条件转成string的where 查询sql
     * @param    array      $where
     * @param    string|null $table
     * @return   string        
     */
    final public static function get_where_sql(array $where, ?string $table = null)
    {
        $query = static::from_module_db(db::select('*'));
        $where = static::_where($query, $where, $table)->_compile_conditions();
        
        $query->reset();
        return $where;
    }

    /**
     * 配置化生成可用的where查询条件
     *  $D = [
     *       'a'       => 1, 
     *       'b'       => 2, 
     *       'c'       => [3, 4],
     *       'd_start' => 5,
     *       'd_end'   => 6,
     *       'e'       => 7,
     *       'f'       => 8,
     *       'g'       => 9,
     *       'h'       => 10,
     *   ];
     *   $arr = mod_agent_server::format_where($D, [
     *       'base_fields'      => ['a' => function($v) { return 'a1'; }, 'b'],
     *       'range_fields'     => ['c' => function($v) { return ['c1', 'c2'];}, 'd'],
     *       'like_fields'      => ['e'],
     *       'or_fields'        => ['f', 'g'],
     *       'left_like_fields' => ['h'],
     *       '>='               => ['d_start'],
     *       '<='               => ['d_end'],
     *   ]);
     *   如果数据库查询字段和值字段不一样的时候，匿名函数可以多加一个引用类型参数，函数体里面定义一下即可,
     *   比如 'base_fields'      => ['a' => function($v, &$n_f) { $n_f = 'fck'; return 'a1'; }, 'b'],
     *   a会变成fck
     *   也可以通过在字段中通过[真实数据库字段名]的方式，比如：
     *   比如 'base_fields'      => ['a[a1]' => 2, 'b[b2]' => 'strtotime'],
     *   a会变成a1 a1 => $D['a']
     * @param    array      $filter_data 数据
     * @param    array      $rules       规则，支持如下
     * base_fields       基础查询
     * rangs_fields      区间查询，值为数组
     * or_fields         or查询
     * like_fields       全模糊查询
     * left_like_fields  左模糊查询
     * right_like_fields 右模糊查询
     * 除了上面的，也可以定于自己想要的类型
     * 也可以使用任何mysql支持的，比如 > | >= | <=....
     * @return   array
    */
    final public static function format_where(array $filter_data, array $rules):array
    {
        $where = [];
        foreach($rules as $type => $fields)
        {
            $type   = strtolower($type);
            $fields = (array) $fields;
            foreach($fields as $k => $v)
            {
                $field     = is_numeric($k) ? $v : $k;
                $new_field = null;
                // 检查是否有匿名字段
                if ( preg_match('#(?<val_f>[^\[\]]+)\[(?<db_f>[^\[\]]+)\]#', $field, $mat) ) 
                {
                    $field     = $mat['val_f'];
                    $new_field = $mat['db_f'];
                }
 
                $field_val = $filter_data[$field] ?? null;
                $field_val = static::_parse_format_where_item($k, $v, $field_val, $new_field);
                // 字段不存在，跳过
                if ( !isset($field_val) )
                {
                    continue;
                }
                // 如果数据库查询字段和值字段不一样的时候，匿名函数可以多加一个引用类型参数，函数体里面定义一下即可
                else if ( $new_field ) 
                {
                    $field = $new_field;
                }

                switch ($type) 
                {
                    case 'eq_fields': case 'base_fields': // 基础查询
                        $where[$field] = $field_val;
                        break;
                    case 'range_fields': // 区间查询
                        // 数组方式
                        if ( !is_array($field_val) ) 
                        {
                            self::exception("range_fields的值必须为数组", static::$unknow_err_status);
                        }

                        $where["{$field}[BETWEEN]"] = $field_val;
                        break;
                    case 'or_fields': // or 操作
                        $where['or'][$field] = $field_val;
                        break;
                    case 'like_fields': // 全模糊
                        $where["{$field}[%]"] = $field_val;
                        break;
                    case 'left_like_fields': // 左模糊
                        $where["{$field}[%$]"] = $field_val;
                        break;
                    case 'right_like_fields': // 右模糊
                        $where["{$field}[$%]"] = $field_val;
                        break;
                    case 'egt_fields': // >= greater than or equal
                        $where["{$field}[>=]"] = $field_val;
                        break;
                    case 'gt_fields': // > greater than
                        $where["{$field}[>]"] = $field_val;
                        break;
                    case 'elt_fields': // >= equal and less than
                        $where["{$field}[<=]"] = $field_val;
                        break;
                    case 'lt_fields': // > less than
                        $where["{$field}[<]"] = $field_val;
                        break;
                    case 'neq':
                        $where["{$field}[!=]"] = $field_val;
                        break;
                    case 'between':
                        if ( !is_array($field_val) ) 
                        {
                            self::exception('between类型，值必须为数组', static::$unknow_err_status);
                        }

                        $where["{$field}[BETWEEN]"] = $field_val;
                        break;
                    default: // 其他mysql支持的查询类型
                        $where["{$field}[{$type}]"] = $field_val;
                        break;
                }
            }
        }

        return $where;
    }

    /**
     * 如果传了函数，则返回调用函数后的结果
     * @param    mixed     $k  
     * @param    mixed     $v  
     * @param    mixed     $val
     * @param    string    $new_field 如果查询字段和值字段不一样的时候，可以传一个变量来接收
     * 'xx1' => function($v, &$new_field) {
     *     $new_field = 'xxx2';
     *     return $v;
     * }
     * @return   mixed         
     */
    private static function _parse_format_where_item($k, $v, $val, ?string &$new_field = null)
    {
        if ( !is_numeric($k) ) 
        {
            // 如果是string，而且可以调用，则认为是系统函数，不能加new_field这个参数，要不会导致报错
            if ( is_string($v) && preg_match('#[\w_]+#', $v) && is_callable($v) ) 
            {
                $val = call_user_func($v, $val);
            }
            else 
            {
                $val = call_user_func_array($v, [$val, &$new_field]);
            }
        }

        return $val;
    }

    /**
     * 用法和where是一样的
     * @param    object     $query 
     * @param    [type]     $having
     * @param    string     $table 
     * @return   object           
     */
    final protected static function _having(object $query, $having, $table = '')
    {
        return self::_where($query, $having, $table, 'having');
    }

    /**
     * @param    object     $query   
     * @param    mixed      $order_by
     * @return   object     $query     
     */
    final protected static function _order_by(object $query, $order_by)
    {
        if ( is_array($order_by) ) 
        {
            foreach($order_by as $column => $direction)
            {
                if ( is_object($direction) )
                {
                    $query->order_by($direction);
                }
                else if ( preg_match('#[^a-z]#i', $column) ) 
                {
                    $query->order_by(self::expr("{$column} {$direction}"));
                }
                else
                {
                    $query->order_by($column, $direction);
                }
            }
        }
        else
        {
            $query->order_by(self::expr($order_by));
        }

        return $query;
    }

    //获取分年的表名
    public static function t($table, $timestamp = FRAME_TIMESTAMP, $format = 'Y')
    {
        $year = date($format, $timestamp);
        return $table .'_'. $year;
    }


    final public static function qoute_field($field, $encode = false)
    {
        $func = !empty($encode) ? 'AES_ENCRYPT' : 'AES_DECRYPT';
        return  "{$func}({$field}, '{$GLOBALS['config']['db']['crypt_key']}')";
    }

    /**
     * 比较复杂的语句可以使用这个函数包一下
     * @param    string     $string
     * @return   object         
     */
    final public static function expr(string $string)
    {
        return db::expr($string);
    }

    /**
     * 直接写sql, 会查询子类配置的数据库
     * @param    string     $sql 
     * @param    bool       $is_master 是否为主库
     * @param    array      $params=>[
     *               'execute'      => true,  // 是否执行，默认执行sql
     *               'close_filter' => true   // close_filter为true 关闭过滤
     *           ] 
     * @return   object
     */
    final public static function query(
        string $sql, 
        ?bool  $is_master = false, 
        array  $params    = ['execute' => true]
    )
    {
        static::_load_module_env();
        if ( static::$table && static::$auto_data_rule ) 
        {
            static::$data_rules = static::filter_rules();
        }

        $execute      = cls_arr::get($params, 'execute', true);
        $close_filter = cls_arr::get($params, 'close_filter', false);
        $query        = static::from_module_db(db::query($sql, $close_filter));
        if ( $execute ) 
        {
            return $query->execute($is_master, $params);
        }
        else
        {
            return $query;
        }
    }

    final public static function get_instance_master_name()
    {
        //需要取主库一般都是开启事物，所以先实例化
        $db_name = db::init_db(
            static::$module_db['name'] ?? null, 
            static::$module_db['config_file'] ?? null
        );

        return !empty(static::$module_db['name']) ? $db_name.'_w' : null;
    }

    /**
     * 多个数据库开启事务才需要用到，返回一个多库事务id给 db_start/db_commit/db_rollback/db_end 使用
     * @param    array      $modules
     * @return   string           
     */
    final public static function muti_start(array $modules)
    {
        $id = md5(serialize($modules));
        if ( !isset(static::$muti_trans[$id]) ) 
        {
            static::$muti_trans[$id] = $modules;
        }

        return $id;
    }

    /**
     * 执行多库事务的方法
     * @param    string     $muti_id
     * @param    string     $func   
     * @return   bool             
     */
    private static function _muti_start_trans(?string $muti_id, string $func)
    {
        if ( $muti_id && isset(static::$muti_trans[$muti_id]) ) 
        {
            foreach(static::$muti_trans[$muti_id] as $m)
            {
                call_user_func([$m, $func]);
            }

            //cli下清除事务id对应的数据
            if ( PHP_SAPI === 'cli' && in_array($func, ['db_commit', 'db_rollback']) ) 
            {
                unset(static::$muti_trans[$muti_id]);
            }

            return true;
        }

        return false;
    }

    /**
     * 开启事物
     * @param  ?string  默认为null, 只会对操作当前定义的数据多，多库事务的时候调用muti_start获取多库事物id
     * @return bool
     */
    final public static function db_start(?string $muti_id = null)
    {
        if ( !static::_muti_start_trans($muti_id, __function__) ) 
        {
            static::_load_module_env();
            db::enable_slave(false);
            static::$enable_slave = true;
            db::start(static::get_instance_master_name());
        }

        return true;
    }

    /**
     * 结束事务,恢复自动提交模式
     * @param  默认为null, 只会对操作当前定义的数据多，多库事务的时候调用muti_start获取多库事物id
     * @return bool
     */
    final public static function db_end(?string $muti_id = null)
    {
        if ( !static::_muti_start_trans($muti_id, __function__) ) 
        {
            static::_load_module_env();
            static::$enable_slave && db::enable_slave(true);
            db::end(static::get_instance_master_name());
        }

        return true;
    }

    /**
     * 提交事务，为了方便发送统计日志，封装一个commit的函数，在commit的时候自动发送
     * 所以模型内如果涉及发送进程结束后发送日志的，commit需要用这个，否则不会发送
     * @param  默认为null, 只会对操作当前定义的数据多，多库事务的时候调用muti_start获取多库事物id
     * @return bool
     */
    final public static function db_commit(?string $muti_id = null)
    {
        if ( !static::_muti_start_trans($muti_id, __function__) ) 
        {
            static::_load_module_env();
            //事务完成后统一发送统计数据
            if ( method_exists('common\extend\pub_func', 'dsrs') )
            {
                util::shutdown_function(
                    ['common\extend\pub_func', 'dsrs'],
                    [null, null, true]
                );
            }

            db::commit(static::get_instance_master_name());
        }

        return true;
    }

    /**
     * 回滚事务，清空缓存中的统计数据
     * @param  ?string  默认为null, 只会对操作当前定义的数据多，多库事务的时候调用muti_start获取多库事物id
     * @return bool
     */
    final public static function db_rollback(?string $muti_id = null)
    {
        if ( !static::_muti_start_trans($muti_id, __function__) ) 
        {
            static::_load_module_env();
            //事务回滚，清空统计日志
            if ( method_exists('common\extend\pub_func', 'dsrs') )
            {
                pub_func::dsrs(null, null, 'clear');
            }

            db::rollback(static::get_instance_master_name());
        }

        return true;
    }

    /**
     * 根据需要把数据中的后台用户id，客户id，商户id 自定义获取用户表中的指定信息
     * 默认只会把用户名取出来分配给映射关系中的value字段，也可以通过$maps_attrs 把其他信心通过implode
     * 连接起来，减少一批数据要联多3个表，查询都是分批主键查询，效率较高,同一张表最多只会查一次
     * 如果需要取其他表的数据，也可以外部定义map，参考maps定义也是可以取到其他表数据的
     * 例如：
     *  $data  = pub_mod_order::order_list();
     *  $data = pub_mod_order::data_map([
     *     'data' => $data,//原始数据
     *      // 如果写成 ['kf_uid' => ['username', 'nickname']] key => array 的方式，则会获取相应字段，直接插入到数据中去
     *      'admin_maps' => ['kf_uid' => 'kf_name', 'kf_admin_id' => 'kf_admin_name'], //后台用户 原始数据中的 uid => 把关联表的数据复制给什么字段
     *      'member_maps' => ['uid' => 'username'],//客户端
     *      'shop_maps' => ['shop_id' => 'shop_name']//商家用户
     *  ]),
     *  admin_maps, member_maps, shop_maps这几个都是需要才写，不需要则不用写的
     *  这样只会按照默认的方式去取数据，即只有用户名，如果想要取除用户名其他信息，可以通过maps_attrs修改默认配置，比如
     *  (下面的member表中的fields重新定义了要取的字段为name和code，用####链接起来，如果后台用户表和商家用户表需要修改，加到数组中去就可以了)
     *  implode为array返回数组
     *  $map_attrs = [
     *      'member_maps' => ['fields' => ['name', 'code'], 'implode' => '####'],
     *      'admin_maps'.......
     *  ];
     *  $data = pub_mod_order::data_map([
     *     'data' => $data,//原始数据
     *      'admin_maps' => ['kf_uid' => 'kf_name', 'kf_admin_id' => 'kf_admin_name'], //后台用户 原始数据中的 uid => 把关联表的数据复制给什么字段
     *      'member_maps' => ['uid' => 'username'],//客户端
     *      'shop_maps' => ['shop_id' => 'shop_name']//商家用户
     *  ], $map_attrs),
     *
     * 运行结果
     *  [0] => Array
     *   (
     *       [order_id] => 111111
     *       [shop_id] => 1
     *       [uid] => 495581a52b00856128fe1ade55909c7a
     *       [amount] => 100
     *       [pay_amount] => 1
     *       [bill_status] => 0
     *       [kf_uid] => 1
     *       [kf_admin_id] => 2
     *       [kf_name] => admin                           新增字段
     *       [kf_admin_name] => test                      新增字段
     *       [member_name] => azhangcompany1##801000411   新增字段
     *   )
     *
     *  如果需要其他的表，也可以通过写maps_atts获取，比如，取钱包的数据
     *   $data = [
     *       0 => ['wid' => 524, 'name' => '1111'],
     *       1 => ['wid' => 525, 'name' => '2222'],
     *   ]
     *
     *   $data = pub_mod_order::data_map(
     *       [
     *           'data' => $data,
     *           'order_map' => ['wid' => 'w_name'],
     *       ],
     *       [
     *           'order_map' => [
     *               'table' => 'mp_wallet',//表名
     *               'index' => 'id',//外键
     *               'fields' => ['uid', 'currency_code']//获取字段
     *               'implode' => '|' 留空返回数组
     *           ],
     *       ]
     *   );
     *  运行结果
     *  Array
     *   (
     *       [0] => Array
     *           (
     *               [wid] => 524
     *               [name] => 111
     *               [w_uid] => c27b6f5b05261298f51d13aa0460530b|CNY
     *           )
     *
     *       [1] => Array
     *           (
     *               [wid] => 525
     *               [name] => 222
     *               [w_uid] => 50d8c583a2b94aa7b73b4fbf2f152b7a|CNY
     *           )
     *
     *   )
     *
     *  $data = self::data_map([
     *       'data'        => $data,
     *       'player_maps' => [
     *           // 把原数据的source_nick_name，增加一个一样的字段source_nick_name到列表数据
     *           'source_account_idx' => ['source_nick_name', 'xx1'....],
     *           // 把原数据的nick_name 增加到列表数组，字段为target_nick_name
     *           'target_account_idx' => ['nick_name' => 'target_nick_name' ... ],
     *       ],
     *       ....
     *   ]);
     * @param  array  $data       数据
     * @param  array  $maps_attrs 覆盖默认映射配置，一般常用的直接写到data_map配置中去
     * @return array  返回原始信息+想取的用户表中的信息
     */
    final public static function data_map($data = array(), $maps_attrs = array())
    {
        $maps  = config::instance('data_map')->get();
        if( !isset($data['data']) || empty($data['data']) ) return [];
        foreach ($maps_attrs as $map => $config) //优先外部配置的属性
        {
            $maps[$map] = array_merge((isset($maps[$map]) ? $maps[$map] : []), (array) $config);
        }

        $fields = $maps_data = $group = [];
        foreach($maps as $map => $row) //获取映射字段信息
        {
            if( isset($data[$map]) )
            {
                $fields[$map] = isset($fields[$map]) ? $fields[$map] : [];
                $fields[$map] = array_merge($fields[$map], array_keys($data[$map]));
            }
        }

        // 区分单条/多条
        $is_single = !is_array(reset($data['data']));
        if ( $is_single ) 
        {
            $data['data'] = [$data['data']];
        }
        
        foreach($data['data'] as $row) //获取映射字段中的值，用值去分表查询
        {
            foreach($fields as $map => $_fields)
            {
                foreach($_fields as $f)
                {
                    if( !isset($row[$f]) ) continue;//字段不存在，跳过
                    $maps_data[$map][$row[$f]] = $row[$f];
                }
            }
        }

        //按批去查
        foreach( $maps_data as $map => $row )
        {
            $_fields = implode(',',  (array) $maps[$map]['fields']).','.$maps[$map]['index'];
            //用户表有扩展表，所以需要单独处理
            if (!empty($maps[$map]['data_func']))
            {
                $tmp = call_user_func_array($maps[$map]['data_func'], [
                    [
                        'fields'             => $_fields,
                        $maps[$map]['index'] => array_keys($row),
                        'index'              => $maps[$map]['index']
                    ]
                ]);
            }
            else
            {
                $where = [$maps[$map]['index'] => array_keys($row)];
                if ( !empty($maps[$map]['where']) ) 
                {
                    $where = array_merge($maps[$map]['where'], $where);
                }
                
                $func = [$maps[$map]['model'] ?? static::class, 'lists'];
                $tmp  = call_user_func_array($func, [[
                    'fields' => $_fields,
                    'table'  => $maps[$map]['table'] ?? null,
                    'where'  => $where,
                    'index'  => $maps[$map]['index']
                ]]);
            }

            if ( !empty($maps[$map]['return_all']) ) 
            {
                 $group[$map] = $tmp;
            }
            else
            {
                $implode = isset($maps[$map]['implode']) ? $maps[$map]['implode'] : '|';
                foreach($tmp as $index => $row)
                {
                    if ( !in_array('*', (array) $maps[$map]['fields']) ) 
                    {
                        $_tmp = [];
                        foreach((array) $maps[$map]['fields'] as $f)
                        {
                            $_tmp[$f] = $row[$f];
                        }
                    }
                    else
                    {
                        $_tmp = $row;
                    }
    

                    $group[$map][$index] = $implode == 'array' ? $_tmp : implode($implode, $_tmp);
                }
            }
        }

        foreach($data['data'] as $key => $row)
        {
            foreach($fields as $map => $_fields)//字段映射
            {
                foreach($_fields as $f)
                {
                    $mf = $data[$map][$f];
                    // 获取配置中全部字段
                    if ( 
                        in_array('*', (array) $mf) && 
                        isset($maps[$map]['fields']) && 
                        is_array($maps[$map]['fields']) 
                    ) 
                    {
                        foreach($maps[$map]['fields'] as $_f)
                        {
                            if ( !isset($row[$f]) || !isset($group[$map][$row[$f]][$_f]) ) 
                            {
                                $data['data'][$key][$_f] = '';
                            }
                            else
                            {
                                $data['data'][$key][$_f] = $group[$map][$row[$f]][$_f];
                            }
                        }
                    }
                    // 获取指定字段
                    else if ( is_array($mf) ) 
                    {
                        foreach($mf as $_fk => $_f)
                        {
                            $_orgin_key = is_numeric($_fk) ? $_f : $_fk;
                            if ( !isset($row[$f]) || !isset($group[$map][$row[$f]][$_orgin_key]) ) 
                            {
                                $data['data'][$key][$_f] = '';
                            }
                            else
                            {
                                $data['data'][$key][$_f] = $group[$map][$row[$f]][$_orgin_key];
                            }
                        }
                    }
                    else if( !isset($row[$f]) || !isset($group[$map][$row[$f]])  )//不存在的字段，跳过
                    {
                        $data['data'][$key][$mf] = isset($maps[$map]['default']) ? $maps[$map]['default'] : '';
                    }
                    else
                    {
                        $data['data'][$key][$mf] = $group[$map][$row[$f]];
                    }

                    //运行callback
                    if( isset($maps[$map]['callback']) && is_callable($maps[$map]['callback']) )
                    {
                        $data['data'][$key][$mf] = call_user_func($maps[$map]['callback'], $data['data'][$key][$mf]);
                    }
                }
            }
        }

        return $is_single ? reset($data['data']) : $data['data'];
    }

    /**
     * 切换默认数据库
     * @return bool
     */
    final public static function switch_default_db()
    {
        return db::switch_db();
    }

    /**
     * 切换模块数据库/默认数据库
     * @param  boolean $switch_module_db
     * @return bool               
     */
    final public static function switch_module_db($switch_module_db = true)
    {
        $result = true;
        if( static::$module_db )
        {
            $name   = $switch_module_db ? current(static::$module_db) : null;
            $result = call_user_func(['kaliphp\db', 'switch_db'], $name);
        }

        return $result;
    }

    /**
     * 加载外表数据
     * pub_mod_model::load([
     *      'is_multi' => 是否是1:n，1=是，0=否,
     *      'data' => 主表数据,
     *      'index' => 关联表id,
     *      'table' => 外表名称,
     *      'foreign_id' => 外键名称,
     *      'fields' => 查询字段，默认*,
     *      'alias' => 键名，默认使用table,
     *      'query_func' => 额外查询回调
     * ]);
     * @param $params
     * @return array
     */
    final public static function load($data, $params)
    {
        self::_load_module_env();
        $data_filter = cls_filter::data([
            'is_multi'   => ['type' => 'int', 'default' => 0],
            'table'      => 'text',
            'foreign_id' => 'text',
            'fields'     => ['type' => 'text', 'default' => '*'],
            'index'      => ['type' => 'text', 'default' => 'id'],
            'alias'      => ['type' => 'text', 'default' => '']
        ], $params, false);

        if (
            empty($data) || 
            empty($data_filter['table']) || 
            empty($data_filter['foreign_id'])
        )
        {
            return [];
        }

        $is_multi   = $data_filter['is_multi'];
        $table      = $data_filter['table'];
        $alias      = empty($data_filter['alias']) ? $table : $data_filter['alias'];
        $foreign_id = $data_filter['foreign_id'];
        $fields     = $data_filter['fields'];
        $index      = $data_filter['index'];
        $is_master  = util::get_value($params, 'is_master', null);

        $ids = [];
        foreach ($data as $item)
        {
            $ids[] = $item[$foreign_id];
        }

        $query = static::from_module_db(db::select($fields))
            ->from($table)
            ->where($index, 'in', $ids);

        if (!empty($params['query_func']) && is_callable($params['query_func']))
        {
            $query->where_open();
            $query = $params['query_func']($query);
            $query->where_close();
        }

        $subs = (array) $query->execute($is_master);
        foreach ($data as $k => $item)
        {
            foreach ($subs as $_k => $sub)
            {
                if ($sub[$index] == $item[$foreign_id])
                {
                    if ($is_multi)
                    {
                        $data[$k][$alias][] = $sub;
                    }
                    else
                    {
                        $data[$k][$alias] = $sub;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 获取json的更新sql函数
     * $sql = static::json_update_sql('json', ['a' => 1, 'b' => 2], ['a']);
     * @param  string $field           json字段名称
     * @param  array  $row             json更新数据
     * @param  array  $json_field_conf json中的有效字段，为空，则为row中全部的key
     * @return 返回合法的json更新sql
     */
    final public static function json_update_sql(string $field, array $row, $json_field_conf = [])
    {
        $sql = '';
        $tmp = [$field];
        foreach($row as $f => $ff)
        {
            //过滤不在json_field_conf的字段
            if ( $json_field_conf && !in_array($f, $json_field_conf) ) continue;
            $tmp[] = util::sprintf(
                '\'$.{field}\', \'{value}\'',
                [
                    'field' => $f,
                    'value' => $ff,
                ]
            );
        }

        count($tmp) > 1 && $sql = db::expr('JSON_SET('.implode(",", $tmp).')');
        return $sql;
    }

    /**
     * 获取exception抛出的异常信息
     * @param    int      $status     
     * @param    ?string  $default_msg
     * @return   string                 
     */
    final public static function get_err_msg(?int $status, ?string $default_msg = null)
    {
        return static::$msg_maps[$status] ?? ($default_msg ?? 'Unknow error!');
    }

    /**
     * 防止业务把一些不安全的错误信息出去，所以业务的异常code不能大于-800
     * @param  \Exception $e 
     * @return string   
     */
    final public static function get_exception_msg(\Exception $e, ?int $code = null)
    {
        $code    = $code ?? $e->getCode();
        $err_msg = $e->getMessage();
        $pattern = '#Duplicate.*entry.*for\s+key\s+\'[^\.]+\.(?<dup_field>[^\s]+)\'#is';
        //重复unique key特殊处理
        if ( preg_match($pattern, $err_msg, $mat) ) 
        {
            $err_msg = "{$mat['dup_field']}已存在";
        }
        else
        {
            //小于1000的，显示系统繁忙，要不很容易把数据库错误都丢出去了
            $err_msg = $code < -1000 ? '系统繁忙，请稍后重试' : $err_msg;
        }
 
        return $err_msg;
    }

    /**
     * 抛异常封装
     * @param  string $msg 
     * @param  int    $code
     * @return Exception  
     */
    final public static function exception(string $msg = '', ?int $code = null)
    {
        $code = $code || $code === 0 ? $code : static::$unknow_err_status;
        throw new \Exception($msg, $code);
    }

    /**
     * 统一处理错误后的status值，防止乱抛出
     * @param  \Exception $e Exception
     * @return int
     */
    final public static function get_exception_status(\Exception $e)
    {
        $err_code = $e->getCode();
        $status   = $err_code >= 0 ? static::$unknow_err_status : $err_code;
        self::$msg_maps[$status] = self::get_exception_msg($e, $status);
        return $status;
    }

    /**
     * 记录错误日志
     * @param    \Exception $e   
     * @param    string     $func 
     * @param    array|null $data
     * @return   void        
     */
    final public static function log_exception(
        \Exception $e, 
        ?string    $func      = null, 
        ?array     $data      = null, 
        ?string    $file_path = null
    )
    {
        if ( $func && !strstr($func, '::') ) 
        {
            $func = (static::class ? static::class . '::' : '') . $func;
        }

        $log = [
            'data'  => $data ?? req::$forms,
            'code'  => $e->getCode(),
            'msg'   => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];

        if ( !$file_path ) 
        {
            log::error($log, $func);
        }
        else
        {
            log::write($file_path, $log);
        }
    }

    /**
     * call_service别名函数
     * @param  mixed $func
     * @param  array  $params
     * @return mixed  大于0表示成功
     */
    final public static function transaction($func, $params = [])
    {
        return static::call_service($func, $params);
    }

    /**
     * 服务层调用模型层公共函数，如果服务层还涉及其他的逻辑，请自己实现
     * @param  mixed $func
     * @param  array  $params
     * @return mixed  大于0表示成功
     */
    final public static function call_service($func, $params = [])
    {
        self::_load_module_env();
        try
        {
            //提交事物前运行钩子
            static::run_hook($func, $params, 'start');
            self::db_start();
            //防止参数带有引用报错
            foreach ($params as &$p) {}
            $status = call_user_func_array($func, $params);
        }
        catch (\Exception $e)
        {
            $err_code = $e->getCode();
            $status   = $err_code >= 0 ? static::$unknow_err_status : $err_code;
            self::$msg_maps[$status] = $e->getMessage();
        }

        if( $status > 0 )
        {
            //提交事务后运行钩子
            static::run_hook($func, $params, 'commit');
            self::db_commit();
        }
        else
        {
            //回滚事物
            static::run_hook($func, $params, 'rollback');
            self::db_rollback();
        }

        self::db_end();
        //事务完成后运行钩子
        static::run_hook($func, $params, 'end');
        //测试环境打印数据库,需要调试的时候再开启
        if( defined('SERVICE_LOG') && SERVICE_LOG )
        {
            log::write(
                'call_service.log',
                [
                    'status' => $status,
                    'func'   => $func,
                    'params' => $params,
                    'sqls'   => var_export(db::$queries, true)
                ]
            );
        }

        return $status;
    }

    /**
     * 如果当前类的方法需要开启事物，只需要方法前加serv_即可（如果还有其他关联业务的，最好开一个service层）
     *public static function __callStatic($method, $args)
     *{
     *   return static::call_serv_static(__class__, $method, $args);
     *}
     * @param  string $method
     * @param  array  $args
     * @param  string $class
     * @return string
     */
    final public static function call_serv_static($class, $method, $args)
    {
        list($prefix, $_method) = @explode('_', $method, 2);
        //开启事物
        if ( strcasecmp($prefix, 'serv') === 0  && method_exists($class, $_method) )
        {
            return static::call_service([$class, $_method], $args);
        }
        //指定数据库
        else if( $_method && method_exists(static::class, $_method) )
        {
            static::set_module_config($prefix);
            return call_user_func_array([static::class, $_method], $args);
        }
        else
        {
            static::exception(
                sprintf('%s:%s is not exits', $class, $method),
                static::$func_not_fund
            );
        }
    }

    /**
     * 运行钩子函数
     * @param  array $func 如果不是一个数组，则会认为是当前类的一个hook,如果是数组，则直接运行
     * @param  array  $data
     * @return mixed  false表示函数存在或者执行返回false
     */
    final public static function run_hook($func, array $data, ?string $suffix = null)
    {
        if ( !is_object($func) ) 
        {
            //替换成hook的路径
            $replace_arr = [
                'model\\'   => 'hook\hook_',
                'service\\' => 'hook\hook_',
            ];
            if ( null !== self::class && !is_array($func) )
            {
                $func  = [static::$class, $func];
            }
             
            if( is_array($func) )
            {
                $func[0] = str_replace(
                    array_keys($replace_arr), 
                    array_values($replace_arr), 
                    $func[0]
                );
            }

            //是否有后缀
            if ( $suffix && is_array($func) ) 
            {
                $func[1] .= '_' . $suffix;
            }

            if ( is_callable($func)  && method_exists($func[0], $func[1]) )
            {
                if ( PHP_SAPI === 'cli' || SYS_DEBUG )
                {
                    return call_user_func_array($func, [$data]);
                }
                else
                {
                    return util::shutdown_function([$func[0], $func[1]], [$data]);
                }
            }
        }

        return false;
    }

    /**
     * 获取系统配置信息
     * @param  string      $module 模块
     * @param  string|null $key    key
     * @return mixed
     */
    final public static function get_config(string $module, ?string $key = null)
    {
        //非cli下取静态变量里的值
        if ( PHP_SAPI != 'cli' )
        {
            static $configs = [];
        }

        if ( strstr($module, ':') )
        {
            list($module, $source) = explode(':', $module);
        }
        else
        {
            $source = null;
        }
        
        $source = $source ?: 'file';
        $configs[$module][$key] = $configs[$module][$key] ?? config::instance($module, $source)->get($key);
        return $configs[$module][$key];
    }

    /**
     * decode 数据库配置中的Json字段
     * @param  array  $data
     * @param  string $table
     * @return array
     */
    final public static function json_format(array $data, ?string $table = null)
    {
        $is_multi = is_array(reset($data));
        if ( $is_multi ) 
        {
            return array_map(function($v) use($table){
                return self::json_format($v, $table);
            }, $data);
        }

        $table       = $table ?? static::$table;
        $json_fields = self::get_config('database', 'json_fields');
        $table       = db::table_prefix($table);
        if ( $json_fields ) 
        {
            $fields = cls_arr::get($json_fields, $table, []);
            foreach($fields as $field)
            {
                if ( isset($data[$field]) )
                {
                    $data[$field] = is_array($data[$field]) ? $data[$field] : json_decode($data[$field], true);
                }
            }
        }

        //格式化bbcode
        $html_fields = self::get_config('database', 'html_fields');
        if ( $html_fields ) 
        {
            $table  = db::table_prefix($table);
            $fields = cls_arr::get($html_fields, $table, []);
            foreach($fields as $field)
            {
                if ( isset($data[$field]) )
                {
                    $data[$field] = cls_bbcode::parse($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * @return   void
     */
    private static function _load_module_env()
    {
        //因为基础类定义了__callStatic，所以这里无法使用is_callable判断
        if ( method_exists(static::class, 'load_module_env') ) 
        {
            call_user_func([static::class, 'load_module_env']);
        }
    }

    /**
     * 并发/者服务器/常驻等一些不可控的因素偶尔会出现因为异常导致程序退出，用这个函数包装一下
     * 使用方法 self::try_catch_func(function() use($a){
     *     var_dump($a);
     * });
     *
     * //可以通过set_mod_data绑定一个调试函数，比如
     * return self::try_catch_func(function() use($data) {
     *    //绑定一个调试函数，会在try_catch_func结束后调用
     *    self::set_mod_data('debug_func', function($status) use($data) {
     *        var_dump($status);
     *        log::error($data);
     *    });
     *    self::exception('test', -1);
     *    return $status;
     *}, true);
     * @param    mixed     $func 
     * @param    mixed     $log_error 如果是数组，只有status是数组种的值的时候才会记录
     * @param    mixed     $exclude_status 需要排除的状态值
     * @return   mixed        
     */
    public static function try_catch_func($func, $log_error = true, array $exclude_status = [])
    {
        //方法不可用直接抛异常
        if ( !is_callable($func) ) 
        {
            static::exception("方法{$func}不可用", static::$unknow_err_status);
        }

        try 
        {
            $status = call_user_func($func);
        } 
        catch (\Exception $e) 
        {
            $status = static::get_exception_status($e);
            //是否记录日志
            if ( 
                $log_error && (!is_array($log_error) || in_array($status, $log_error)) &&
                (!$exclude_status || !in_array($status, $exclude_status))
            ) 
            {
                //只拿上一层的调用信息
                $debug_info = debug_backtrace(0, 2)[1] ?? [];
                //获取调用函数名，如果是数组，直接使用
                if ( is_array($func) ) 
                {
                    $tmp = $func;
                }
                //字符串或者闭包方式
                else
                {               
                    $tmp  = [
                        static::class,
                        is_string($func) ? $func :
                        ($debug_info['function'] ?? '')
                    ];
                }
            
                $func_name = implode('::', $tmp);
                static::log_exception($e, $func_name, $debug_info['args'] ?? []);
                pub_mod_system_error::trigger_error($e->getMessage() . "[{$status}]");
            }
        }
        
        //是否有绑定调试函数
        if ( ($debug_func = static::get_mod_data('debug_func')) && is_callable($debug_func) ) 
        {
            static::set_mod_data('debug_func', null);
            self::try_catch_func(function() use($debug_func, $status) {
                call_user_func($debug_func, $status);
            });
        }

        return $status;
    }

    /**
     * 获取表过滤规则
     * @param    stirng|null $action
     * @return   array
     */
    final public static function filter_rules(
        ?string $action = null, 
        ?array  $assign_rules = null, 
        bool    $use_default = false
    )
    {
        static $rules = [];
        if ( static::$table && empty($rules[static::$table]) ) 
        {
            static::init_table(static::$table);
            foreach(static::$table_ifnos['fields'] as $field => $conf)
            {
                if ( 'add' == $action && $conf['Key'] == 'PRI' ) 
                {
                    continue;
                }

                $type = 'text';
                $maps = ['int' => 'int', 'float' => 'float'];
                foreach ($maps as $k => $v)
                {
                    if ( stristr($conf['Type'], $k) ) 
                    {
                        $type = $v;
                        break;
                    }
                }

                $rules[static::$table][$field] = [
                    'type'     => $type,
                    'rtype'    => $conf['Type'],
                    'is_pk'    => $conf['Key'] == 'PRI',
                    'required' => 'edit' != $action && !$conf['Null'] && !$conf['Default'],
                    'default'  => 'edit' != $action && $use_default && strlen($conf['Default']) ? $conf['Default'] : null,
                ];

                //是否有自定义的属性
                if ( !empty($assign_rules[$field]) && is_array($assign_rules[$field]) ) 
                {
                    $rules[static::$table][$field]= util::array_merge_multiple(
                        $rules[static::$table][$field], 
                        $assign_rules[$field]
                    );
                }
            }

            $rules[static::$table]['_config_'] = ['filter_null' => true];
        }
        // var_dump($rules);exit;
        return $rules[static::$table] ?? [];
    }

    public static function filter_rule_default(array $rules)
    {
        foreach($rules as &$rule)
        {
            if ( isset($rule['type']) && !isset($rule['default']) && !$rule['is_pk'] ) 
            {
                $rule['default'] = $rule['type'] == 'int' ? 0 : '';
            }
        }

        return $rules;
    }

    /**
     * 获取表的规则规则
     * @param    array      $append_rules  需要添加/覆盖的规则,字段设置为null，会删除
     * @param    array      $unset_fields  需要删除的规则
     * @return   array                  
     */
    final public static function get_table_data_rules(array $append_rules = [], array $unset_fields = [])
    {
        $data_rules = static::$data[static::class]['data_rule'] ?? [];
        if ( $data_rules ) 
        {
            foreach($unset_fields as $f)
            {
                unset($data_rules, $f);
            }

            $data_rules = array_filter(array_merge($data_rules, $append_rules));
        }

        return $data_rules;
    }



    /**
     * 设置当前类数据,一般用于model处理过程中，想往外，比如control输出一些信息的时候可以用使用
     * @param    string     $key            
     * @param    mixed      $data           
     * @return   bool
     */
    final public static function set_mod_data(string $key, $data)
    {
        if ( $data ) 
        {
            static::$data[static::class][$key] = $data;
            //最大保存1000个key
            $max_length = 1000;
            if ( isset(static::$data[static::class]) && count(static::$data[static::class]) > $max_length ) 
            {
                static::$data[static::class] = array_slice(static::$data[static::class], -$max_length);
            }
        }
        else if( isset(static::$data[static::class][$key]) )
        {
            unset(static::$data[static::class][$key]);
        }

        return true;
    }

    /**
     * 获取类型数据信息
     * @param    string|null $key 不传返回全部
     * @return   mixed           
     */
    final public static function get_mod_data(?string $key = null, ?string $default = null)
    {
        return !$key ? (static::$data[static::class] ?? $default)  : 
        (isset(static::$data[static::class]) ? (static::$data[static::class][$key] ?? $default) : $default);
    }

    /**
     * 根据过滤规则生成测试数据
     * @param    array      $rules
     * @return   array         
     */
    final public static function generate_test_data(array $rules)
    {
        $ret = [];
        foreach ($rules as $field => $rule)
        {
            if ( !isset($rule['type']) || $field == static::$pk ) continue;
            switch ($rule['rtype']) 
            {
                case 'datetime':
                    $ret[$field] = date('Y-m-d H:i:s');
                    break;
                case 'date':
                    $ret[$field] = date('Y-m-d');
                    break;
                case 'year':
                    $ret[$field] = date('Y');
                    break;
                case 'float':
                case 'int':
                case in_array($rule['type'], ['int', 'float']):
                    if ( preg_match('#time#i', $field) ) 
                    {
                        $ret[$field] = time();
                    }
                    else
                    {
                        $ret[$field] = rand(1, 10);
                    }
                    
                    break;
                default:
                    $ret[$field] = uniqid();
                    break;
            }
        }

        return $ret;
    }

    /**
     * 统一处理不带分页/带分页，单条数据格式化函数，达到共用formatter
     * formatter的值可以为：
     * true    则默认为调用当前类的format_data
     * 字符串   先回尝试是否为可执行方法，否则就是当前类方法
     * 数组     [class, func]
     * 匿名函数  function($data) {
     *     foreach($data as &$v)
     *     {
     *         //格式化
     *     }
     *
     *     return $data;
     * }
     * 指定的formatter函数必须是处理2维数组的逻辑
     * @param    array      $data     单条数据/不分页2维数组/带分页结构
     * @param    mixed     $formatter 匿名函数/数组/当前类函数名
     * @return   array
     */
    final public static function data_formatter(array $data, $formatter = 'format_data'):array
    {
        if ( !$data || !$formatter ) 
        {
            return $data;
        }
 
        // 判断是否存在数据data
        if ( isset($data['data']) &&  is_array($data['data']) ) 
        {
            $keys     = array_keys($data['data']);
            $has_data = !is_string(reset($keys)); // number or null
        }

        $has_data  = $has_data ?? false;
        $is_single = !$has_data && !cls_arr::is_multiple_arr($data, 2);

        // 单条数据
        if ( $is_single ) 
        {
            $new  = [$data];
            $data = $new;
        }

        // formatter不可用，则尝试调用类
        $formatter = is_callable($formatter) ? $formatter : 
        [static::class, is_bool($formatter) ? 'format_data' : $formatter];
        // 如果数据中存在data,而且为数组，则认为是分页数据结构
        if ( $has_data )
        {
            $data['data'] = $data['data'] ? call_user_func($formatter, $data['data']) : [];
        }
        else
        {
            $data = call_user_func($formatter, $data);
        }

        return $is_single ? reset($data) : $data;
    }

    /**
     * 获取当前数据库配置信息
     * @param    ?string     $key
     * @return   mixed         
     */
    final public static function get_db_config(?string $key)
    {
        return db::get_config(static::get_db_name(), $key);
    }

    /**
     * 根据语言，返回当前语言列表数据
     * @param    array      $data        列表数据
     * @param    string     $table       当前表
     * @param    string     $lang        当前语言，一般使用默认
     * @param    string     $pk          当前表主键，不填会自动获取，但是不保证100%对
     * @return   array
     */
    final public static function lang_data(
        array   $data, 
        ?string $table = null, 
        ?string $lang  = null, 
        ?string $pk    = null
    )
    {
        $table = $table ?? static::$table;
        $pk    = $pk    ?? static::get_pk(['table' => $table]);
        return pub_mod_muti_lang::detect_lang_data($table, $data, $lang, $pk);
    }

    /**
     * 获取时间偏移db对象
     * @param    int        $diff        
     * @param    string     $interval_str
     * @return   string
     */
    protected static function get_time_interval(int $diff, string $interval_str = 'MONTH')
    {
        return self::expr(sprintf(
            ' NOW() - interval %d %s',
            $diff,
            $interval_str
        ));
    }

}
