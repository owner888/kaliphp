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

namespace kaliphp;

use kaliphp\config;
use kaliphp\util;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\req;
use kaliphp\resp;
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
        // E_STRICT          => "<font color='#D63107'>运行时错误</font>",
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
        if ( req::method() == 'CLI' )
        {
            return;
        }

        // exception_handler 是直接调用的 error_handler
        // 如果 error_handler 函数还抛出异常，这里就会到这里来
        // $last_error = error_get_last();

        // Set a mark point for benchmarking
        cls_benchmark::mark('loading_time:_base_classes_end');

        // 输出HTML
        tpl::output();

        // 如果有错误信息则显示，有警告的情况下，会导致已经输出的 json 后面又拼接这个错误 json，导致前端无法解开
        self::show_error();

        // 运行放进后台的操作
        util::shutdown_function(null, array(), true);

        // $config = config::instance('cache')->get();
        // $sess_config = $config['session'];
        // 这里的执行在session::write()之前，释放会导致session无法写入
        // 所以session如果采用cache方式，这里不要释放，在 sesson::write() 里面释放
        // 没有启动session 或者 session类型不是cache的情况下
        // 反正是长链，缓存不要关了，否则session_regenerate_id会出问题
        // if ( !session_id() || $sess_config['type'] != 'cache' ) 
        // {
        //     cache::free();
        // }
    }


    /**
     * 错误接管函数
     *
     * trigger_error 直接到这里来
     * throw new Exception 先到 exception_handler，再到这里来
     * trigger_error 不会中断程序，只是警告；Exception 会中断程序
     * 8.x 开始 error_handler 只接收 4 个参数，所以 $errcontext 需要给一个默认值
     *
     */
    public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext = [])
    {
        $err = self::format_errstr($errno, $errstr, $errfile, $errline, $errcontext);
        // 存在错误信息
        if ( $err != '@' )
        {
            log::debug("\nError Trace:\n".self::strip_tags($err));
            // CLI下面没必要保存日志到最后，直接debug里面输出即可
            if ( PHP_SAPI != 'cli' ) 
            {
                self::$_debug_error_msg .= $err;
            }
        }
    }

    /**
     * exception 接管函数
     */
    public static function exception_handler($e)
    {
        $errno      = $e->getCode();
        $errstr     = self::fmt_code($errno, $e->getMessage());
        $errfile    = $e->getFile();
        $errline    = $e->getLine();
        $errcontext = $e->getTrace();
        self::error_handler($errno, $errstr, $errfile, $errline, $errcontext);
    }

    /**
     * 性能测试托管接口函数
     */
    public static function xhprof_handler()
    {
        if ( PHP_SAPI !== 'cli' && function_exists('xhprof_enable') && SYS_DEBUG )
        {
            defined('PATH_LIBRARY') or define('PATH_LIBRARY',   './lib');
            $xhprof_data = \xhprof_disable();
            include PATH_LIBRARY. "/debug/xhprof_lib/utils/xhprof_lib.php";
            include PATH_LIBRARY. "/debug/xhprof_lib/utils/xhprof_runs.php";
            $xhprof_runs = new \XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
            echo "<div style='font-size:11px;padding-bottom:10px;position:fixed;bottom:2px;left:21px;' align='center'><a target='_blank' href='/core/library/debug/xhprof_html/index.php?run=$run_id&source=xhprof_foo'>xhprof性能日志</a></div>";
        }
    }

    /**
     * 显示调试信息（程序结束时执行）
     * 仅在 shutdown_handler 里调用
     * CLI 不会跑到这里来，在 error_handler 方法里面 _debug_error_msg 没有被赋值
     */
    public static function show_error()
    {
        if ( self::$_debug_error_msg != '' || self::$_debug_mt_info !='' )
        {
            if ( ( SYS_DEBUG === true || self::$_debug_safe_ip === true ) && !self::$_debug_hidden )
            {
                // API接口不需要返回那么详细的html内容
                if ( req::is_json() ) 
                {
                    resp::response_error(-500, self::$_debug_error_msg);
                }
                else 
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
    public static function format_errstr($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $user_errors = [ E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE ];

        // 处理从 catch 过来的错误
        if ( in_array($errno, $user_errors) )
        {
            foreach ( $errcontext as $e )
            {
                if ( is_object($e) && method_exists($e, 'getMessage') ) 
                {
                    $errno      = $e->getCode();
                    $errstr     = $errstr.' '.$e->getMessage();
                    $errline    = $e->getLine();
                    $errfile    = $e->getFile();
                    $errcontext = $e->getTrace();
                }
            }
        }

        // 生产环境不理会普通的警告错误
        // $not_save_error = [ E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_NOTICE, E_USER_WARNING, E_WARNING ];
        // if ( SYS_DEBUG !== true && !in_array($errno, $not_save_error) )
        // {
        //    return '@';
        // }

        // 错误文件不存在
        if ( !is_file($errfile) )
        {
            return '@';
        }

        // 读取源码指定行
        $fp = fopen($errfile, 'r');
        $n = 0;
        $errline_str = '';
        while( !feof($fp) )
        {
            $line = fgets($fp, 1024);
            $n++;
            if ( $n == $errline ) 
            {
                $errline_str = trim($line);
                break;
            }
        }
        fclose($fp);

        // 如果错误行用 @ 进行屏蔽，不显示错误
        if ( $errline_str[0] == '@' || preg_match("/[\(\t ]@/", $errline_str) ) 
        {
            return '@';
        }

        // API接口不需要返回那么详细的html内容
        if ( req::is_json() ) 
        {
            return $errstr . ' in ' . $errfile . ' ' . $errline;
        }

        // 错误类型不存在
        if ( !isset(self::$_debug_errortype[$errno]) )
        {
            self::$_debug_errortype[$errno] = "<font color='#466820'>手动抛出</font>";
        }

        $err = "<div style='font-size:14px;line-height:160%;border-bottom:1px dashed #ccc;margin-top:8px;'>\n";
        $err .= "发生环境：" . date("Y-m-d H:i:s", time()) . '::' . req::cururl() . "<br />\n";
        $err .= "错误类型：" . self::$_debug_errortype[$errno] . "<br />\n";
        $err .= "出错原因：<font color='#3F7640'>" . $errstr . "</font><br />\n";
        $err .= "提示位置：<a href=\"" . str_replace([ '%file','%line' ], [ $errfile, $errline ], SYS_EDITOR) . "\">" . $errfile . "</a> 第 {$errline} 行<br />\n";
        $err .= "断点源码：<font color='#747267'>" . htmlspecialchars($errline_str) . "</font><br />\n";
        $err .= "详细跟踪：<br />\n";

        $backtrace = debug_backtrace();
        array_shift($backtrace);
        $narr = [ 'class', 'type', 'function', 'file', 'line' ];
        foreach ( $backtrace as $i => $trace )
        {
            foreach ( $narr as $k )
            {
                if ( !isset($trace[$k]) ) $trace[$k] = '';
            }
            $err .= "<font color='#747267'>[$i] in function {$trace['class']}{$trace['type']}{$trace['function']} ";
            if ($trace['file']) $err .= " in {$trace['file']} ";
            if ($trace['line']) $err .= " on line {$trace['line']} ";
            $err .= "</font><br />\n";
        }

        $err .= "<span></span></div>\n";

        return $err;
    }

    /**
     * 过滤掉html标签，记录日志只需要干净的文本即可 
     * 
     * @param mixed $errstr 错误信息 
     * 
     * @return string
     */
    public static function strip_tags($errstr)
    {
        //$errstr = str_replace(array("<font color='#3F7640'>","</font>"), array("\033[33;1m","\033[0m"), $errstr);
        $errstr = preg_replace("/<font([^>]*)>|<\/font>|<\/div>|<\/strong>|<strong>|<br \/>/iU", '', $errstr);
        $errstr = preg_replace("/<div style='font-size:14px([^>]*)>/iU", "-----------------------------------------------\n错误跟踪：", $errstr);
        $errstr = preg_replace("/<span><\/span>/iU", "-----------------------------------------------", $errstr);
        $errstr = str_replace(array("-&lt;","&gt;"), array("<",">"), $errstr);
        $errstr = strip_tags($errstr);

        return $errstr;
    }

    /**
     * 格式化代码为字符串
     *
     * @param  int     $code
     * @param  string  $errstr
     *
     * @return string
     */
    public static function fmt_code(int $errno, string $errstr = '') :string
    {
        $msgtpl = config::instance('exception')->get($errno);
        if ( empty($msgtpl)) 
        {
            return $errstr;
        }

        // 如果是序列化数据，反序列化
        $msg  = util::is_serialized($errstr) ? unserialize($errstr) : $errstr;
        if ( !is_array($msg)) 
        {
            $msg = [$msg];
        }

        return vsprintf($msgtpl, $msg);
    }

    /**
     * 手动指定内存占用测试函数 
     * 
     * @param mixed $optmsg optmsg 
     * 
     * @return void
     */
    public static function test_debug_mt($optmsg)
    {
        if ( SYS_DEBUG === true || self::$_debug_safe_ip )
        {
            if ( empty(self::$_debug_mt_time) )
            {
                self::$_debug_mt_time = microtime(true);
                $m = sprintf('%0.2f', memory_get_usage()/1024/1024);
                self::$_debug_mt_info = "{$optmsg}: 当前内存 {$m} MB<br />\n";
            }
            else
            {
                $cutime = microtime(true);
                $etime = sprintf('%0.4f', $cutime - self::$_debug_mt_time);
                $m = sprintf('%0.2f', memory_get_usage()/1024/1024);
                self::$_debug_mt_info .= "{$optmsg}: 当前内存 {$m} MB 用时：{$etime} 秒<br />\n";
                self::$_debug_mt_time = $cutime;
            }
        }
    }

}

