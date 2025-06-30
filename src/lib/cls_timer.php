<?php
namespace kaliphp\lib;

use kaliphp\util;
use kaliphp\config;
use Channel\Client;
use Workerman\Lib\Timer;

class cls_timer
{
    public static $server_ip       = null; //当前服务器ID

    public static $posix_pid       = 0;    //当前进程端口

    public static $server_id       = 0;    //自定义服务id

    public static $channel         = null; //通知频道

    public static $configs         = [];   //服务器配置信息

    public static $flag            = null; //定时器id连接符

    protected static $channel_rule = 'broadcast:timer:%d:%d';

    /**
     * 初始化定时器
     * 
     * @return void
     */
    public static function _init()
    {
        self::$server_ip = util::get_server_ip();
        self::$posix_pid = posix_getpid();
        self::$configs   = config::instance('server')->get();
        self::$server_id = self::$configs['servers'][self::$server_ip] ?? 1;
        self::$channel   = sprintf(self::$channel_rule, self::$server_id, self::$posix_pid);
        self::$flag      = ':';

        Client::connect(self::$configs['channel_server_ip'], self::$configs['channel_server_port']);
        // 只监听当前服务器，当前端口号的频道
        Client::on(self::$channel, function($data) {
            if ( empty($data['cmd']) ) return;
            switch ($data['cmd']) 
            {
                case 'del':
                    Timer::del($data['timer_id']);
                    break;
                default:
                    // code...
                    break;
            }
        });
    }

    /**
     * 获取定时器id
     * 
     * @param  int    $timer_id 
     * @return string
     */
    protected static function get_timer_id(int $timer_id)
    {
        $timer_id  = implode(self::$flag, [self::$server_id, self::$posix_pid, $timer_id, uniqid()]);
        // log::write('timer_log', "添加定时器 {$timer_id}", __METHOD__);
        return $timer_id;
    }

    /**
     * 根据定时器id获取服务器/端口号/真实定时器id
     * 
     * @param  mixed $timer_id 
     * @return array           
     */
    public static function get_timer_data($timer_id)
    {
        if ( strstr($timer_id, self::$flag) ) 
        {
            $timer_data = explode(self::$flag, $timer_id);
        }
        else
        {
            $timer_data = [self::$server_id, self::$posix_pid, $timer_id, uniqid()];
        }
        
        return $timer_data;
    }

    /**
     * 添加定时器
     * 
     * @param  float   $time_interval 
     * @param  mixed   $callback      
     * @param  array   $args          
     * @param  boolean $persistent    
     */
    public static function add($time_interval, $callback, $args = array(), $persistent = true)
    {
        $timer_id = Timer::add($time_interval, $callback, $args, $persistent);
        $timer_id = self::get_timer_id($timer_id);
        return $timer_id;
    }

    /**
     * 删除定时器(当前服务器和端口的不需要走通知)
     * 
     * @param  string $timer_id 
     * @return viod
     */
    public static function del(string $timer_id)
    {
        // log::write('timer_log', "删除定时器 {$timer_id}", __METHOD__);
        list($server_id, $posix_pid, $timer_id, $uniqid) = self::get_timer_data($timer_id);
        if ( 
            $server_id == self::$server_id && 
            $posix_pid == self::$posix_pid 
        ) 
        {
            Timer::del($timer_id);
        }
        else
        {
            // 发送给指定的服务器的进程
            Client::publish(sprintf(self::$channel_rule, $server_id, $posix_pid), [
                'cmd'      => 'del',
                'timer_id' => $timer_id
            ]);
        }
    }
}