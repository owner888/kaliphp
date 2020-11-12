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
    // session类型 default || cache
    public static $session_type = '';

    // session过期时间
    public static $session_expire = 1440;

    public static function _init()
    {
        $config = config::instance('cache')->get('session');
        self::$session_type   = $config['type']   ?? 'cache';
        self::$session_expire = $config['expire'] ?? ini_get('session.gc_maxlifetime');
    }

    public static function handle()
    {
        // 启动gc概率 = gc_probability/gc_divisor
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);

        // close the name session
        session_id() and session_write_close();

        if ( self::$session_type == 'cache' ) 
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
     * 页面执行了session_start 后首先调用的函数
     *
     * @param string $save_path     session 保存路径
     * @param string $session_name  session 名称
     * @return bool
     */
    public static function init(string $save_path, string $session_name = 'PHPSESSID'): bool
    {
        // 必须返回true/false
        return true;
    }

    /**
     * 读取指定id的session数据
     *
     * @param string $id    SESSION ID
     *
     * @return string       SESSION DATA
     */
    public static function read(string $id): string
    {
        // 必须返回string
        $value = cache::get('session_'.$id);
        return (string)$value;
    }

    /**
     * 写入指定id的session数据
     *
     * @param string $id         SESSION ID
     * @param string $sess_data  SESSION DATA
     *
     * @return bool         TRUE/FALSE  
     */
    public static function write($id, $sess_data): bool
    {
        // 针对不同用户不同设置不同的过期时间
        if ( !empty(kali::$auth->user['session_expire']) )
        {
            self::$session_expire = kali::$auth->user['session_expire'];
        }
        if ( $sess_data ) 
        {
            cache::set('session_'.$id, $sess_data, self::$session_expire);
        }

        // 启动 session 的情况下，这个方法是最后执行的，比 register_shutdown_function 还要后面
        // 反正是长链，缓存不要关了，否则 session_regenerate_id 会出问题
        //cache::free();

        // 必须返回true/false
        return true;
    }

    /**
     * 清理接口
     * 因为缓存时间到期会自动清除，所以这里不需要实现
     *
     * @param $max_lifetime     
     *
     * @return bool             true/false
     */
    public static function gc($max_lifetime)
    {
        return true;
    }

    /**
     * 注销指定id的session
     *
     * @param $id       SESSION ID
     *
     * @return bool     true/false
     */
    public static function destroy($id)
    {
        self::del($id);

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

    //--------------------------------------------------------------------------------------------
    // 外部调用函数
    //--------------------------------------------------------------------------------------------

    /**
     * 删除session 
     * 
     * @param string $id    SESSION ID
     * 
     * @return void
     */
    public static function del($id)
    {
        return cache::del('session_'.$id);
    }

    /**
     * 添加session 
     * 
     * @param string $id    SESSION ID
     * @param string $key   SESSION KEY 
     * @param mixed  $value SESSION VALUE 
     * 
     * @return bool     true/false
     */
    public static function add(string $id, string $key = null, $value): bool
    {
        $sess_data = self::decode(self::read($id));
        $sess_data[$key] = $value;
        return self::write($id, self::encode($sess_data));
    }

    /**
     * 获取session 
     * 
     * @param string $id    SESSION ID
     * @param string $key   SESSION KEY 
     * 
     * @return mixed
     */
    public static function get(string $id, string $key = null)
    {
        if( strlen($id) == 0 )
        {
            return [];
        }

        $sess_data = self::decode(self::read($id));

        if ( $key ) 
        {
            $sess_data = $sess_data[$key] ?? '';
        }

        return $sess_data;
    }

    public static function ttl($id)
    {
        if ( self::$session_type == 'default' ) 
        {
            return time() - filemtime(session_save_path().'/sess_'.$id);
        }

        return cache::ttl('session_'.$id);
    }

    public static function encode(array $sess_data): string
    {
        if ( empty($sess_data)) 
        {
            return '';
        }

        $encode_data = '';

        foreach ($sess_data as $key => $value) 
        {
            $encode_data .= $key . '|' . serialize($value);
        }

        return $encode_data;
    }

    public static function decode(string $sess_data): array
    {
        if( strlen($sess_data) == 0 )
        {
            return [];
        }

        // match all the session keys and offsets
        preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $sess_data, $matches, PREG_OFFSET_CAPTURE);

        if ( empty($matches[2])) 
        {
            return [];
        }

        $decode_data = [];

        $last_offset = null;
        $current_key = '';
        foreach ( $matches[2] as $value )
        {
            $offset = $value[1];
            if(!is_null($last_offset))
            {
                $value_text = substr($sess_data, $last_offset, $offset - $last_offset);
                $decode_data[$current_key] = unserialize($value_text);
            }
            $current_key = $value[0];
            $last_offset = $offset + strlen($current_key) + 1;
        }

        $value_text = substr($sess_data, $last_offset);
        $decode_data[$current_key] = unserialize($value_text);

        return $decode_data;
    }

}

/* vim: set expandtab: */

