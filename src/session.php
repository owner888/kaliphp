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

namespace kaliphp;
use kaliphp\kali;

/**
 * session接口类
 * 正常页面访问
 * init -> read -> gc -> PHP Core -> write -> close
 * 用户手动调用session_destroy()
 * init -> read -> destroy -> close -> PHP Core
 * 用户手动调用session_regenerate_id()
 * init -> read -> write -> close --> init -> read -> PHP Core -> write -> close
 *
 * @version $Id$  
 */   
class session
{
    public static $config = [];

    // session类型 default || cache
    public static $session_type = '';

    // session过期时间
    public static $session_expire = 1440;

    public static function _init()
    {
        $cache_config = config::instance('cache')->get();
        self::$config = $cache_config['session'];
        self::$session_type = self::$config['type'];
        self::$session_expire = empty(self::$config['expire']) 
            ? ini_get('session.gc_maxlifetime') 
            : self::$config['expire'];
    }

    public static function handle()
    {
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);

        // close the name session
        session_id() and session_write_close();


        if ( self::$config['type'] == 'cache' ) 
        {
            session_set_save_handler(
                "kaliphp\session::init", "kaliphp\session::close", "kaliphp\session::read", 
                "kaliphp\session::write", "kaliphp\session::destroy", "kaliphp\session::gc"
            );
        }
        else 
        {
            //session_save_path( PATH_CACHE );
        }

    }

    /**
     * 页面执行了session_start后首先调用的函数
     *
     * @param $save_path
     * @param $session_name
     * @return bool
     */
    public static function init( $save_path, $session_name )
    {
        // 必须返回true/false
        return true;
    }

    /**
     * 读取指定id的session数据
     *
     * @param $id           SESSION ID
     * @return string       SESSION DATA
     */
    public static function read( $id )
    {
        // 必须返回string
        $value = cache::get( 'session_'.$id );
        return (string)$value;
    }

    /**
     * 写入指定id的session数据
     *
     * @param $id           SESSION ID
     * @param $sess_data    SESSION DATA
     * @return bool         TRUE/FALSE  
     */
    public static function write( $id, $sess_data )
    {
        // 针对不同用户不同设置不同的过期时间
        if ( !empty(kali::$auth->user['session_expire']) )
        {
            self::$session_expire = kali::$auth->user['session_expire'];
        }
        if ( $sess_data ) 
        {
            cache::set( 'session_'.$id, $sess_data, self::$session_expire );
        }
        // 启动session的情况下，这个方法是最后执行的，比register_shutdown_function还要后面
        // 反正是长链，缓存不要关了，否则session_regenerate_id会出问题
        //cache::free();
        // 必须返回true/false
        return true;
    }

    /**
     * 清理接口
     * 因为缓存时间到期会自动清除，所以这里不需要实现
     *
     * @param $max_lifetime     
     * @return bool             true/false
     */
    public static function gc( $max_lifetime )
    {
        return true;
    }

    /**
     * 注销指定id的session
     *
     * @param $id       SESSION ID
     * @return bool     true/false
     */
    public static function destroy( $id )
    {
        self::del( $id );
        return true;
    }

    /**
     * 关闭接口（页面结束会执行）
     *
     * @return bool     true/false
     */
    public static function close()
    {
        // 这里千万不能关闭缓存，因为后面可能还有代码需要用到缓存
        //cache::free();
        return true;
    }

    //-------------------------------------------------------------
    // 外部调用函数
    //-------------------------------------------------------------
    public static function del( $id )
    {
        return cache::del( 'session_'.$id );
    }

    public static function ttl( $id )
    {
        if ( self::$session_type == 'default' ) 
        {
            return time() - filemtime(session_save_path().'/sess_'.$id);
        }

        return cache::ttl( 'session_'.$id );
    }

}

/* vim: set expandtab: */

