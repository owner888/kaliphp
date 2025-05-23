<?php
namespace kaliphp\extend;

use kaliphp\{
    log,
    req,
    util,
};

/**
 * trait_exception 异常处理
 */
trait trait_exception
{
    public static
        $unknow_err_status = -1211, // 未知错误,一般都是数据库死锁
        $msg_maps          = [],   // 错误映射
        $df_err_msg        = '系统繁忙，请稍后重试'; // 默认显示错误提示

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
     * @param    mixed     $final_func 最后调用函数
     * @return   mixed        
     */
    final public static function try_catch_func($func, $log_error = true, ?array $exclude_status = null, $final_func = null)
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
                // util::shutdown_function(
                //     [pub_mod_system_error::class, 'notify_exception'],
                //     [$e]
                // );
            }
        }
        finally 
        {
            $has_get_mod_data_func = false;
            if ( !$final_func && method_exists(static::class, 'get_mod_data') ) 
            {
                $has_get_mod_data_func = true;
                $final_func = static::get_mod_data('debug_func');
            }
            
            //是否有绑定调试函数
            if ( $final_func && is_callable($final_func) ) 
            {
                $has_get_mod_data_func && static::set_mod_data('debug_func', null);
                $status = $status ?? null;
                self::try_catch_func(function() use($final_func, $status) {
                    call_user_func($final_func, $status);
                });
            }
        }

        return $status;
    }

    /**
     * 获取exception抛出的异常信息
     * @param    int      $status     
     * @param    ?string  $default_msg
     * @param    int      $lt_code 小于多少的code,显示
     * @return   string                 
     */
    final public static function get_err_msg(?int $status = null, ?string $default_msg = null, ?int $lt_code = -1000)
    {
        if ( $status && isset($lt_code) && $status < $lt_code ) 
        {
            return static::$df_err_msg;
        }
        else if ( !isset($status) && static::$msg_maps ) 
        {
            return reset(static::$msg_maps);
        }

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
            $err_msg = "{$mat['dup_field']}已存在[Duplicate]";
        }
 
        return $err_msg;
    }

    /**
     * 当前错误是否为记录已存在错误
     * @param    int        $status
     * @return   boolean           
     */
    final public static function is_duplicate_error(?int $status)
    {
        $err_msg = static::get_err_msg($status);
        if ( $err_msg && strstr($err_msg, '已存在[Duplicate]') ) 
        {
            return true;
        }

        return false;
    }

    /**
     * 抛异常封装
     * @param  string $msg 
     * @param  int    $code
     * @return \Exception  
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
        $status   = $err_code > 0 ? static::$unknow_err_status : $err_code;
        self::$msg_maps[$status] = self::get_exception_msg($e, $status);
        return $status;
    }

    /**
     * 检查参数是否为数组,用于cls_filter::data 返回的数据判断
     * @param    mixed       $data    
     * @param    mixed       $err_maps
     * @param    int|integer $err_code
     * @return   void
     */
    final public static function detect_is_array($data, $err_maps = [], int $err_code = -1)
    {
        return self::detect_is_not_empty(
            is_array($data), 
            (
                !is_array($data) ? 
                (is_array($err_maps) ? ($err_maps[$data] ?? "参数[{$data}]有误") : $err_maps) : 
                null
            ), 
            $err_code
        );
    }

    /**
     * 断言数据是否为空
     * @param    mixed       $data    
     * @param    array       $err_maps
     * @param    int|integer $err_code
     * @return   void             
     */
    final public static function detect_is_not_empty($data, ?string $err_msg = null, ?int $err_code = -1)
    {
        if( !$data )
        {
            static::exception($err_msg, $err_code);
        }
    }

    /**
     * 断言data为空
     * @param    mixed       $data    
     * @param    array       $err_maps
     * @param    int|integer $err_code
     * @return   void             
     */
    final public static function detect_is_empty($data, ?string $err_msg = null, ?int $err_code = -1)
    {
        if( $data )
        {
            static::exception($err_msg, $err_code);
        } 
    }

    /**
     * 检查是否小于1
     * @param    mixed       $status    可以是int/string/bool/函数    
     * @param    array       $err_maps
     * @param    int|integer $err_code
     * @return   void
     */
    final public static function detect_is_success($status, ?string $err_msg = null, ?int $err_code = -1)
    {
        if ( !is_numeric($status) && is_callable($status) ) 
        {
            $status = call_user_func($status);
        }

        return static::detect_is_not_empty($status > 0, $err_msg ?? static::get_err_msg($status), $err_code);
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

        log::write($file_path ?: 'try_catch_func_excepption', $log);
    }
}