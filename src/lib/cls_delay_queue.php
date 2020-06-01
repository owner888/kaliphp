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

namespace kaliphp\lib;

use kaliphp\config;
use kaliphp\log;
use kaliphp\util;
use kaliphp\lib\cls_redis;
use kaliphp\lib\cls_redis_lock;

/**
 * 延时队列
 *
 * 添加数据：
 * cls_delay_queue::instance()
 *     ->use_tube('fuck')
 *     ->put($data, $delay, $priority);
 *     
 * 订阅tube:
 * cls_delay_queue::instance()
 *     ->use_tube('fuck')
 *     //->lock() //多进程消费才需要
 *     //->bury() //消费后放到预留队列，一般不需要
 *     //->block() //一般不需要设置，默认阻塞了1/10秒
 *     ->reserve();
 *     
 * 预留队列推送到release队列
 * cls_delay_queue::instance()->kick($num)
 *
 * 
 */
class cls_delay_queue 
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
    protected static $config = [];

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @var array
     */
    private $_atts = [];

    /**
     * @var array
     */
    private static $_instances = [];

    private $_queue_name = [
        'list_tube'  => 'list_tube:%s', //tube列表
        'tube_delay' => 'tube:delay:%s',//delay队列
        'tube_bury'  => 'tube:bury:%s', //bury队列
    ];

    public static function _init()
    {
        //self::$config = config::instance('delay_queue')->get();
    }

    /**
     * 单例
     * @param string $name
     * @param array config
     * @return db
     */
    public static function instance( $name = 'delay_queue', array $config = null )
    {
        if (!isset(self::$_instances[$name]))
        {
            self::$_instances[$name] = new self($name, $config);
        }

        return self::$_instances[$name];
    }

    public function __construct($name = 'delay_queue', $config = null)
    {
        $this->_name   = $name;
        $this->_config = $config ? $config : self::$config;
    }

    /**
     * 实例化一个对象
     * @param  array  $config 
     * @return object
     */
    private function _handler(array $config = [])
    {
        if ( !is_object($this->_handler) ) 
        {
            $redis_conf = !empty($config['redis_conf']) ? $config['redis_conf'] : null;
            $this->_handler = cls_redis::instance($this->_name);
        }

        return $this->_handler;
    }

    /**
     * 指定使用的tube,支持多次调用，订阅多个tube
     * @param  string $tube 
     * @return $this      
     */
    public function use_tube($tube = 'default')
    {
        if ( $tube && !isset($this->_atts['all_tubes'][$tube]) ) 
        {
            $this->_atts['all_tubes'][$tube] = [
                'tube'      => $this->get_queue_name($tube),
                'tube_bury' => $this->get_queue_name($tube, 'tube_bury'),
            ];

            $this->_atts['total_tubes'] = count($this->_atts['all_tubes']);
        }

        //如果没有则随机一个
        if ( !isset($this->_atts['tube_rand']) ) 
        {
            $this->_atts['tube_rand'] = $this->_atts['total_tubes']-1;
        }
        //如果只监控一个tube,则不需要改变tube_rand
        else if ( $this->_atts['total_tubes'] > 1 )
        {
            //如果等于total_tubes-1，则重新返回第一个
            if ( $this->_atts['tube_rand'] ==  $this->_atts['total_tubes'] -1 ) 
            {
                $this->_atts['tube_rand'] = 0;
            } 
            else
            {
                $this->_atts['tube_rand']++;
            }
        }

        $keys = array_keys($this->_atts['all_tubes']);
        $this->_atts['current'] = $this->_atts['all_tubes'][$keys[$this->_atts['tube_rand']]];
        return $this;
    }

    /**
     * use_tube 别名函数
     * @param  string $tube 
     * @return $this      
     */
    public function watch(string $tube = 'default')
    {
        return $this->use_tube($tube);
    }

    /**
     * 添加延时数据
     * @param  mix  $data     数据
     * @param  int  $delay    默认为0
     * @param  int  $priority 优先级0～5
     * @return int  大于0表示添加成功
     */
    public function put($data, int $delay = 0, int $priority = 0, array $options = [])
    {
        $_delay = $delay;
        //回调函数不能用
        if ($data instanceof \Closure) 
        {
            throw new \Exception("Error data");
        }
        else if ( 0 <= ($diff = $delay-time()) ) 
        {
            $_delay = $diff;
        }
        else
        {
            $delay += time();
        }

        //设置优先级
        if ( $priority < 0 || $priority > 5 ) 
        {
            throw new \Exception("Error priority value");
        }
        else if ( $priority ) 
        {
            $delay -= $priority/10;
        }

        //以时间作为score，对任务队列按时间从小到大排序
        $status = $this->_handler()->zadd(
            $this->get_current_tube(),
            $delay,
            $this->_data(array_merge([
                'payload' => $data,
                'delay'   => $_delay
            ], $options))
        );

        return $status;
    }

    /**
     * 重新返回release队列
     * @param  mix  $job      job任务数据，为空表示当前的任务
     * @param  int  $delay    默认为0
     * @param  int  $priority 优先级0～5
     * @return bool           大于0表示成功
     */
    public function release($job = null, $delay = 0, $priority = 0)
    {
        if ( false != ($job = $this->get_current_job($job)) ) 
        {
            if ( isset($this->_atts['job']) ) 
            {
                unset($this->_atts['job']);
            }

            $delay = $delay ? $delay : $job['delay'];
            $job['releases']++;

            return $this->put($job['payload'], $delay, $priority, $job);
        }

        return false;
    }

    /**
     * 预定消息
     * @param  integer $block_millisecond    阻塞时间（微秒）
     * @param  boolean $after_reserve_delete 预定后是否删除，如果为false,需要自己手动删除
     * @return mix                           null or array
     */
    public function reserve($block_millisecond = null, $after_reserve_delete = true)
    {
        while ( true ) 
        {
            $this->use_tube(null);
            //获取任务，以0和当前时间为区间，返回一条记录
            $data = $this->block($block_millisecond)->_handler()
                ->zRangeByScore(
                    $this->get_current_tube('tube'), 0, microtime(true), 
                    ['limit' => [0, 1]]
                );

            if ( $data ) 
            {
                $data = reset($data);
                //对于多进程消费，需要上锁，取得锁才可以返回
                if ( 
                    !empty($data['job_id']) &&
                    !empty($this->_atts['lock_second']) && 
                    false == cls_redis_lock::lock(
                        "delay_queue:lock:{$data['job_id']}", 
                        0, 
                        $this->_atts['lock_second']
                    )
                ) 
                {
                    continue;
                }

                $this->_atts = array_merge($this->_atts, [
                    'job'     => $data,
                    'reserve' => true,
                ]);

                $after_reserve_delete && $this->delete($data);
            }
            
            break;
        }

        return $data ? $data : null;
    }

    /**
     * 把消息放到冻结队列
     * @param  mix $job 如果为空，则冻结当前预选的job
     * @return int      大于0表示成功
     */
    public function bury($job = null)
    {
        $status = false;
        if ( false != ($job = $this->get_current_job($job)) ) 
        {
            if ( isset($this->_atts['job']) ) 
            {
                unset($this->_atts['job']);
            }
    
            $status = $this->_handler()->zadd(
                $this->get_current_tube('tube_bury'),
                time(),
                $this->_data(array_merge($job, [
                    'buries' => $job['buries']+1
                ]))
            );
        }

        return $status;
    }

    /**
     * 删除任务
     * @param  mix  $job 如果不传则删除当前的job
     * @return bool 大于0表示成功
     */
    public function delete($job = null, $queue_name = null)
    {
        $status = false;
        if ( false != ($job = $this->get_current_job($job)) ) 
        {
            $queue_name = $queue_name ? $queue_name : $this->get_current_tube('tube');
            if ( !$queue_name ) 
            {
                throw new \Exception("Error queue_name");
            }

            $status = $this->_handler()->zRem($queue_name, $job);
        }

        return $status;
    }

    /**
     * 把预留的数据重新推到relase队列
     * @param  int $max 默认1条
     * @return int 成功的数量
     */
    public function kick(int $max = 1)
    {
        //获取任务，以0和当前时间为区间，返回一条记录
        $data = $this->_handler()
            ->zRangeByScore(
                $this->get_current_tube('tube_bury'), 0, microtime(true), 
                ['limit' => [0, $max]]
            );

        if ( $data ) 
        {
            foreach($data as $job)
            {
                $this->release($job);
                $this->delete($job, $this->get_current_tube('tube_bury'));
            }
        }

        return count($data);
    }

    /**
     * 对于多进程消费，需要上锁，取得锁才可以返回
     * @param  integer $lock_second 上锁时间
     * @return $this
     */
    public function lock($lock_second = 1)
    {
        $this->_atts['lock_second'] = $lock_second;
        return $this;
    }

    /**
     * 设置阻塞时长
     * @param  integer $millisecond 微妙数
     * @return $this
     */
    public function block($millisecond = null)
    {
        $millisecond = $millisecond ? $millisecond : 10000;
        if ( $millisecond < 100 ) 
        {
            throw new \Exception("Error millisecond value");
        }

        usleep($millisecond);
        return $this;
    }


    /**
     * 获取队列名称
     * @param  string $name 
     * @param  string $type 
     * @return string
     */
    protected function get_queue_name(string $name, string $type = 'tube_delay')
    {
        if ( !isset($this->_queue_name[$type]) ) 
        {
            throw new \Exception("Error queue type");
        }

        return sprintf($this->_queue_name[$type], $name);
    }

    /**
     * 格式化数据
     * @param  array  $data 数据
     * @return array  返回格式化后的数据
     */
    private function _data(array $data)
    {
        $default = [
            'job_id'      => uniqid(), //任务id
            'releases'    => 0,        //READY/DELAYED状态的次数
            'buries'      => 0,        //任务休眠次数
            'reserves'    => 0,        //被消费的次数
            'delay'       => 0,        //延时秒数
            'create_time' => time(),   //创建时间
            'payload'     => [],       //数据
        ];

        foreach($default as $f => $ff)
        {
            if ( isset($data[$f]) ) 
            {
                $default[$f] = $data[$f];
            }
        }

        if ( !empty($this->_atts['reserve']) ) 
        {
            $default['reserves']++;
        }

        return $default;
    }

    /**
     * 返回当前tube
     * @Author han
     * @param  string $tube_type tube类型
     * @return string            
     */
    protected function get_current_tube($tube_type = 'tube')
    {
        if ( !isset($this->_atts['current'])  ) 
        {
            $this->use_tube(util::get_value($this->_config, 'default_tube', 'default'));
        }

        if ( !isset($this->_atts['current'][$tube_type]) ) 
        {
            throw new \Exception("Error tube_type");
            
        }

        return $this->_atts['current'][$tube_type];
    }

    /**
     * 获取当前的job
     * @param  [type] $job [description]
     * @return [type]      [description]
     */
    protected function get_current_job($job = null)
    {
        $job = $job ? $job : (!empty($this->_atts['job']) ? $this->_atts['job'] : null);
        return $job;
    }

    public function __destory()
    {
        unset($this->_atts);
    }
}


