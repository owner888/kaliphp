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
use kaliphp\config;
use kaliphp\util;
use kaliphp\cache;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\req;
use kaliphp\lib\cls_benchmark;

class errorhandler
{
    public static $config = [];

    // 安全IP，调试关闭后依然可以显示调试信息的IP，一般是开发公司的IP，方便程序员进行调试
    public static $_debug_safe_ip = false;
    // 错误信息
    public static $_debug_error_msg = '';
    // 打点调试[消耗的时间]
    public static $_debug_mt_time = '';
    // 打点调试[消耗的内存]
    public static $_debug_mt_info = '';

    /**
     * 如果开启了debug模式，仍然不想显示debug的信息
     * 通常是ajax/api类接口，可以在操作的页面或控制器中调用 cls_debug::debug_hidden() 方法把这个变量改为 true
     */
    private static $_debug_hidden = false;

    /**
     * 错误类型数组
     * 实际上错误句柄函数并不能处理 E_ERROR、E_PARSE、E_CORE_ERROR、E_CORE_WARNING、E_COMPILE_ERROR、 E_COMPILE_WARNING
     * 下面会列出上面几种只是用作参考
     */
    public static $_debug_errortype = array (
        E_WARNING         => "<font color='#CDA93A'>警告</font>",
        E_NOTICE          => "<font color='#CDA93A'>普通警告</font>",
        E_USER_ERROR      => "<font color='#D63107'>用户错误</font>",
        E_USER_WARNING    => "<font color='#CDA93A'>用户警告</font>",
        E_USER_NOTICE     => "<font color='#CDA93A'>用户提示</font>",
        E_STRICT          => "<font color='#D63107'>运行时错误</font>",
        E_ERROR           => "致命错误",
        E_PARSE           => "解析错误",
        E_CORE_ERROR      => "核心致命错误",
        E_CORE_WARNING    => "核心警告",
        E_COMPILE_ERROR   => "编译致命错误",
        E_COMPILE_WARNING => "编译警告"
    );

    public static function _init()
    {
        self::$config = config::instance('config')->get('security');

        if ( in_array(req::ip(), self::$config['safe_client_ip'])) 
        {
            self::$_debug_safe_ip = true;
        }
    }

    /**
     * 是否隐藏调试信息
     */
    public static function debug_hidden( $hidden = true )
    {
        self::$_debug_hidden = $hidden;
    }

    /**
     * 程序结束后执行的动作
     */
    public static function shutdown_handler()
    {
        // exception_handler 是直接调用的 error_handler
        // 如果 error_handler 函数还抛出异常，这里就会到这里来
		$last_error = error_get_last();

        // Set a mark point for benchmarking
        cls_benchmark::mark('loading_time:_base_classes_end');

        if (req::method() !== 'CLI')
        {
            // 输出HTML
            tpl::output();
        }  

        // 如果有错误信息，显示
        self::show_error();

        // 运行放进后台的操作
        util::shutdown_function(null, array(), true);

        //$config = config::instance('cache')->get();
        //$sess_config = $config['session'];
        // 这里的执行在session::write()之前，释放会导致session无法写入
        // 所以session如果采用cache方式，这里不要释放，在 sesson::write() 里面释放
        // 没有启动session 或者 session类型不是cache的情况下
        // 反正是长链，缓存不要关了，否则session_regenerate_id会出问题
        //if( !session_id() || $sess_config['type'] != 'cache' ) 
        //{
            //cache::free();
        //}
    }
    
    
    /**
     * 错误接管函数
     * trigger_error 直接到这里来
     * throw new Exception 先到handle_exception，再到这里来
     * trigger_error 不会中断程序，只是警告，excetion会中断程序
     */
    public static function error_handler($code, $message, $file, $line, $vars)
    {
        $log_type = 'debug';

        //ajax和api接口直接输出json
        if ( req::is_ajax() ) 
        {
            $log_type = 'ajax';
        }
        $err = self::format_errmsg($log_type, $code, $message, $file, $line, $vars);
        if( $err != '@' )
        {
            self::$_debug_error_msg .= $err;
        }
    }

    /**
     * exception接管函数
     */
    public static function exception_handler($e)
    {
        $code    = $e->getCode();
        $message = $e->getMessage();
        $message = self::fmt_code($code, $message);
        $file    = $e->getFile();
        $line    = $e->getLine();
        $trace   = $e->getTrace();
        self::error_handler($code, $message, $file, $line, $trace);
    }

    /**
     * 性能测试托管接口函数
     */
    public static function xhprof_handler()
    {
        if(PHP_SAPI !== 'cli' && function_exists('xhprof_enable') && DEBUG_MODE === true)
        {
            $xhprof_data = xhprof_disable();
            include PATH_LIBRARY. "/debug/xhprof_lib/utils/xhprof_lib.php";
            include PATH_LIBRARY. "/debug/xhprof_lib/utils/xhprof_runs.php";
            $xhprof_runs = new XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
            echo "<div style='font-size:11px;padding-bottom:10px;position:fixed;bottom:2px;left:21px;' align='center'><a target='_blank' href='/core/library/debug/xhprof_html/index.php?run=$run_id&source=xhprof_foo'>xhprof性能日志</a></div>";
        }
    }

    /**
     * 显示调试信息（程序结束时执行）
     * 仅在 handler_php_shutdown 里调用
     */
    public static function show_error()
    {
        // ajax/app接口报错
        if ( req::is_ajax() && self::$_debug_error_msg != '' ) 
        {
            log::debug("Error Trace:\n".self::$_debug_error_msg);

            if( ( SYS_DEBUG === true || self::$_debug_safe_ip === true ) && !self::$_debug_hidden )
            {
                echo json_encode(array(
                    'code' => -1,
                    'msg'  => self::$_debug_error_msg
                ));
            }
            else 
            {
                echo json_encode(array(
                    'code' => -1,
                    'msg'  => 'System Error'
                ));
            }
            // 直接返回不要处理下面的html错误格式化
            return;
        }


        if( self::$_debug_error_msg != '' || self::$_debug_mt_info !='' )
        {
            $errmsg = self::$_debug_error_msg;
            //$errmsg = str_replace(array("<font color='#3F7640'>","</font>"), array("\033[33;1m","\033[0m"), $errmsg);
            $errmsg = preg_replace("/<font([^>]*)>|<\/font>|<\/div>|<\/strong>|<strong>|<br \/>/iU", '', $errmsg);
            $errmsg = preg_replace("/<div style='font-size:14px([^>]*)>/iU", "-----------------------------------------------\n错误跟踪：", $errmsg);
            $errmsg = str_replace(array("-&lt;","&gt;"), array("<",">"), $errmsg);
            $errmsg = strip_tags($errmsg);

            log::debug("\nError Trace:\n".$errmsg);

            if ( PHP_SAPI == 'cli') 
            {
                echo $errmsg;
            }
            else 
            {
                if( ( SYS_DEBUG === true || self::$_debug_safe_ip === true ) && !self::$_debug_hidden )
                {
                    $js  = '<script language=\'javascript\'>';
                    $js .= 'function debug_close_all() {';
                    $js .= '    document.getElementById(\'debug_ctl\').style.display=\'none\';';
                    $js .= '    document.getElementById(\'debug_errdiv\').style.display=\'none\';';
                    $js .= '}</script>';
                    echo $js;
                    echo '<div id="debug_ctl" style="width:100px;line-height:18px;position:absolute;top:2px;left:2px;border:1px solid #ccc; padding:1px;text-align:center">'."\n";
                    echo '<a href="javascript:;" onclick="javascript:document.getElementById(\'debug_errdiv\').style.display=\'block\';" style="font-size:12px;">[打开调试信息]</a>'."\n";
                    echo '</div>'."\n";
                    echo '<div id="debug_errdiv" style="z-index:9999;width:80%;position:absolute;top:10px;left:8px;border:2px solid #ccc; background: #fff; padding:8px;display:none">';
                    echo '<div style="line-height:24px; background: #FBFEEF;;"><div style="float:left"><strong>Kali框架应用错误/警告信息追踪：</strong></div><div style="float:right"><a href="javascript:;" onclick="javascript:debug_close_all();" style="font-size:12px;">[关闭全部]</a></div>';
                    echo '<br style="clear:both"/></div>';
                    echo self::$_debug_error_msg;
                    echo "<hr /><div>";
                    echo "<strong>性能追踪：</strong><br />".self::$_debug_mt_info."</div>\n";
                    echo '<br style="clear:both"/></div>';
                }
            }
        }
    }

    /**
     * 格式化错误信息
     */
    public static function format_errmsg($log_type='debug', $errno, $errmsg, $filename, $linenum, $vars)
    {
        $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

        //处理从 catch 过来的错误
        if (in_array($errno, $user_errors))
        {
            foreach($vars as $k=>$e)
            {
                if( is_object($e) && method_exists($e, 'getMessage') ) 
                {
                    $errno     = $e->getCode();
                    $errmsg    = $errmsg.' '.$e->getMessage();
                    $linenum   = $e->getLine();
                    $filename  = $e->getFile();
                    $backtrace = $e->getTrace();
                }
            }
        }

        //生产环境不理会普通的警告错误
        $not_save_error = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_NOTICE, E_USER_WARNING, E_WARNING);
        if( SYS_DEBUG !== true && !in_array($errno, $not_save_error) )
        {
            return '@';
        }

        //读取源码指定行
        if( !is_file($filename) )
        {
            return '@';
        }

        $fp = fopen($filename, 'r');
        $n = 0;
        $error_line = '';
        while( !feof($fp) )
        {
            $line = fgets($fp, 1024);
            $n++;
            if( $n==$linenum ) 
            {
                $error_line = trim($line);
                break;
            }
        }
        fclose($fp);

        //如果错误行用 @ 进行屏蔽，不显示错误
        if( $error_line[0]=='@' || preg_match("/[\(\t ]@/", $error_line) ) 
        {
            return '@';
        }

        // 如果是ajax/app请求，返回文本错误信息
        if( $log_type=='ajax' )
        {
            return $errmsg = "Fatal error:  $errmsg in {$filename}:{$linenum}";
        }

        // 如果是html，返回html错误信息
        $err = '';
        if( $log_type=='debug' )
        {
            $err = "<div style='font-size:14px;line-height:160%;border-bottom:1px dashed #ccc;margin-top:8px;'>\n";
        }
        else
        {
            if( !empty($_SERVER['REQUEST_URI']) )
            {
                $script_name = $_SERVER['REQUEST_URI'];
                $nowurl = $script_name;
            } 
            else
            {
                $script_name = $_SERVER['PHP_SELF'];
                $nowurl = empty($_SERVER['QUERY_STRING']) ? $script_name : $script_name.'?'.$_SERVER['QUERY_STRING'];
            }

            //替换不安全字符
            $f_arr_s = array('<', '*', '#', '"', "'", "\\", '(');
            $f_arr_r = array('〈', '×', '＃', '“', "‘", "＼", '（');
            $nowurl = str_replace($f_arr_s, $f_arr_r, $nowurl);

            $nowtime = date('Y-m-d H:i:s');
            $err = "Time: ".$nowtime.' @URL: '.$nowurl."\n";
        }

        if( empty(self::$_debug_errortype[$errno]) )
        {
            self::$_debug_errortype[$errno] = "<font color='#466820'>手动抛出</font>";
        }

        $error_line = htmlspecialchars($error_line);

        //$err .= "<strong>PHPCALL框架应用错误跟踪：</strong><br />\n";
        $err .= "发生环境：" . date("Y-m-d H:i:s", time()).'::' . req::cururl() . "<br />\n";
        $err .= "错误类型：" . self::$_debug_errortype[$errno] . "<br />\n";
        $err .= "出错原因：<font color='#3F7640'>" . $errmsg . "</font><br />\n";
        //$err .= "提示位置：" . $filename . " 第 {$linenum} 行<br />\n";
        $err .= "提示位置：<a href=\"".str_replace(array('%file','%line'), array($filename, $linenum),SYS_EDITOR)."\">" . $filename . "</a> 第 {$linenum} 行<br />\n";
        $err .= "断点源码：<font color='#747267'>{$error_line}</font><br />\n";
        $err .= "详细跟踪：<br />\n";

        $backtrace = debug_backtrace();
        array_shift($backtrace);
        $narr = array('class', 'type', 'function', 'file', 'line');
        foreach($backtrace as $i => $l)
        {
            foreach($narr as $k)
            {
                if( !isset($l[$k]) ) $l[$k] = '';
            }
            $err .= "<font color='#747267'>[$i] in function {$l['class']}{$l['type']}{$l['function']} ";
            if($l['file']) $err .= " in {$l['file']} ";
            if($l['line']) $err .= " on line {$l['line']} ";
            $err .= "</font><br />\n";
        }

        $err .= $log_type=='debug' ? "</div>\n" : "------------------------------------------\n";

        return $err;
    }

    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    public static function fmt_code($code, $message)
    {
        $msgtpl = config::instance('exception')->get($code);
        if ( empty($msgtpl)) 
        {
            return $message;
        }

        // 如果是序列化数据，反序列化
        $msg  = util::is_serialized($message) ? unserialize($message) : $message;
        if ( !is_array($msg)) 
        {
            $msg = [$msg];
        }

        return vsprintf($msgtpl, $msg);
    }

    /**
     * 手动指定内存占用测试函数
     * @parem $optmsg
     * return void
     */
    public static function test_debug_mt( $optmsg )
    {
        if( SYS_DEBUG === true || self::$_debug_safe_ip )
        {
            if( empty(self::$_debug_mt_time) )
            {
                self::$_debug_mt_time = microtime(true);
                $m = sprintf('%0.2f', memory_get_usage()/1024/1024);
                self::$_debug_mt_info = "{$optmsg}: 当前内存 {$m} MB<br />\n";
            }
            else
            {
                $cutime = microtime(true);
                $etime = sprintf('%0.4f', $cutime - $_debug_mt_time);
                $m = sprintf('%0.2f', memory_get_usage()/1024/1024);
                self::$_debug_mt_info .= "{$optmsg}: 当前内存 {$m} MB 用时：{$etime} 秒<br />\n";
                self::$_debug_mt_time = $cutime;
            }
        }
    }

}

