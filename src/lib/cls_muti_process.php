<?php
/**
 * Kali is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KALI
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       https://doc.kaliphp.com
 */

namespace kaliphp\lib;
use kaliphp\log;

/**
 * 多进程执行任务
 * 调用方式：
 * 1.闭包方式
 * cls_muti_process::instance()->insert(function() use(xxx1, xx2){
 *     //想干嘛干嘛
 * });
 * 2.函数方式
 * cls_muti_process::instance()->insert(['class', 'func'], $params);
 * 3.类似crond方式
 * cls_muti_process::instance()->insert('xxx.php', null, true);
 *
 * 重复推任务后，最后执行execute更能体现多进程的速度
 * cls_muti_process::instance()->execute();
 */
class cls_muti_process
{
    //最大fork的进程数量
    private $_max_workers = 8;

    //主进程的pid
    private $_master_pid = 0;

    //开启的子进程的pid
    private $sub_pids = [];

    //子进程退出标识符
    private $_exit_status = 0;

    //需要执行的任务
    private $_stack_jobs = [];

    //实例名称
    private $_name = 'default';

    //是否有定时器
    private $_clock = false;

    //实例数组
    private static $_instances = [];

    public static function instance($name = null, $worker_process_num = null)
    {
        if( !function_exists('pcntl_fork') )
        {
            throw new \Exception("fork fail", -1001);
        }
        else if( !isset(static::$_instances[$name]) )
        {
            static::$_instances[$name] = new self($name, $worker_process_num);
        }

        return static::$_instances[$name];
    }

    /**
     * 构造函数，根据池的初始化检测用户登录信息
     *
     * @param $config   链接配置
     */
    public function __construct($name = null, $worker_process_num = null)
    {
        $this->_name        = isset($name) ? $name : $this->_name;
        $this->_max_workers = isset($worker_process_num) ? $worker_process_num : $this->_max_workers;
        $this->_master_pid  = posix_getpid();
    }

    /**
     * 添加一个任务,注意，调用常驻脚本中的函数，需要自己手动调用posix_kill(posix_getpid(), SIGTERM);
     * 否则主进程会组是在pcntl_wait这个函数，导致程序没法向下运行
     * 调用方式：
     * 1.闭包方式
     * cls_muti_process::instance()->insert(function() use(xxx1, xx2){
     *     //想干嘛干嘛
     * });
     * 2.函数方式
     * cls_muti_process::instance()->insert(['class', 'func'], $params);
     * 3.类似crond方式
     * cls_muti_process::instance()->insert('xxx.php', null, true);
     * @Author han
     * @param  mix     $func    函数/文件
     * @param  array   $params  函数传递的参数/文件不可用
     * @param  boolean $is_file 是否为文件
     * @return 返回当前对象
     */
    public function insert($func, $params = [], $is_file = false)
    {
        if(
            (!$is_file && is_callable($func)) ||
            ($is_file && file_exists($func))
        )
        {
            $this->_stack_jobs[] = array(
                'func'    => $func,
                'params'  => $params,
                'is_file' => $is_file
            );
        }

        return $this;
    }

    /**
     * 执行函数/文件
     * @Author han
     * @return int 返回开启的进程数
     */
    public function execute($params = [])
    {
        if( !$this->_stack_jobs ) return false;
        // echo "execute():".$this->_name;
        // var_dump($this->_stack_jobs);exit;
        $length     = ceil(count($this->_stack_jobs) / $this->_max_workers);
        $stack_jobs = array_chunk($this->_stack_jobs, $length);
        $max_works  = count($stack_jobs);
        for ($work_id = 0; $work_id < $this->_max_workers; $work_id++)
        {
            if( !isset($stack_jobs[$work_id]) ) break;
            //创建子进程,返回子进程id
            $pid = pcntl_fork();
            //错误处理：创建子进程失败时返回-1.
            if( $pid == -1 )
            {
                die('Could not fork');
            }
            //父进程会得到子进程号，所以这里是父进程执行的逻辑
            else if( $pid )
            {
                $this->sub_pids[$pid] = $pid;
            }
            //子进程因为获取不到子进程id的,子进程得到的$pid为0,这里执行子进程逻辑
            else
            {
                $this->_run($work_id, $stack_jobs[$work_id]);
                exit($this->_exit_status);
            }
        }

        $this->_stack_jobs = [];
        $this->_clock      = false;

        //监控子进程执行状态
        empty($params['is_clock']) && $this->_monitor_worker();
        return $max_works;
    }

    /**
     * 监控子进程
     * @Author han
     * @return void
     */
    private function _monitor_worker()
    {
        if ( $this->_master_pid === posix_getpid() )
        {
            $try_times = 0;
            $max_trys  = $this->_max_workers*10;
            while (true)
            {
                //等待子进程中断，防止子进程成为僵尸进程。
                $pid         = pcntl_wait($call_status, WUNTRACED);
                $call_status = pcntl_wexitstatus($call_status);
                if ($call_status === $this->_exit_status)
                {
                    unset($this->sub_pids[$pid]);
                }

                if ( empty($this->sub_pids) || ++$try_times > $max_trys )
                {
                    break;
                }
            }
        }
    }

    /**
     * 子进程运行函数/文件
     * @Author han
     * @param  [type] $work_id [description]
     * @param  [type] $jobs    [description]
     * @return [type]          [description]
     */
    private function _run($work_id, $jobs)
    {
        foreach($jobs as $job)
        {
            if ($job)
            {
                //运行crond文件
                if( $job['is_file'] && file_exists($job['func']) )
                {
                    include $job['func'];
                }
                else
                {
                    call_user_func_array($job['func'], $job['params']);
                }
            }
        }

        //防止常驻脚本，子进程不会退出
        posix_kill(posix_getpid(), SIGTERM);
    }

}
