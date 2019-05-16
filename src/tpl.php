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
use kaliphp\lib\cls_benchmark;
use kaliphp\lib\cls_profiler;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * 模板引擎实现类
 *
 * @author seatle<seatle@foxmail.com>
 * @version $Id$
 */
class tpl
{
    public static $config = [];

    private static $_instance = null;

    public static $template_dir = null;
    public static $compile_dir = null;
    public static $cache_dir = null;
    public static $plugin_dir = null;

    // 自定义模版标签填充数据用
    public static $blocksdata = array();

    // 最终输出的数据
    public static $output;
    
    /**
     * Smarty 初始化
     * @return resource
     */
    public static function _init()
    {
        if (self::$_instance === null)
        {
            self::$config = config::instance('config')->get('template');
            $backtrace = debug_backtrace();
            $file = end($backtrace);
            $root_path = dirname($file['file']);

            if (!self::$template_dir) 
            {
                self::$template_dir = $root_path.DS.'template';
            }

            if (!self::$compile_dir) 
            {
                self::$compile_dir = $root_path.DS.'data'.DS.'template'.DS.'compile';
            }
            if (!self::$cache_dir) 
            {
                self::$cache_dir = $root_path.DS.'data'.DS.'template'.DS.'cache';
            }

            self::$_instance = new \Smarty();
            self::$_instance->setTemplateDir(self::$template_dir);
            self::$_instance->setCompileDir(util::path_exists( self::$compile_dir ));
            self::$_instance->setCacheDir(util::path_exists( self::$cache_dir ));
            self::$_instance->addPluginsDir(__dir__.DS.'lib'.DS.'smarty_plugins');

            self::$_instance->setLeftDelimiter(self::$config['left_delimiter']);
            self::$_instance->setRightDelimiter(self::$config['right_delimiter']);
            self::$_instance->setCompileCheck(self::$config['compile_check']);
            //self::$_instance->force_compile = true;
            //self::$_instance->debugging = true;
            //self::$_instance->caching = true;
            //self::$_instance->cache_lifetime = 120;
            foreach (self::$config['filters'] as $k=>$v) 
            {
                //self::$_instance->load_filter($k, $v);
            }
            self::config();
        }

        return self::$_instance;
    }
    
    protected static function config ()
    {
        self::$_instance->assign('request', req::$forms);
        self::$_instance->assign('clear_cache', '?' . time());
    }
    
    public static function assign ($tpl_var, $value)
    {
        self::$_instance->assign($tpl_var, $value);
    }
    
    // 这个方法应该去掉，否则不在kali框架下面会有问题
    public static function output ()
    {
        $elapsed = cls_benchmark::elapsed_time('total_execution_time_start', 'total_execution_time_end');

        // 替换模板中执行时间、消耗内存的占位符
        if (strpos(self::$output, '{elapsed_time}') !== false or strpos(self::$output, '{memory_usage}') !== false)
        {
            $memory	= round(memory_get_usage() / 1024 / 1024, 2).'MB';
            self::$output = str_replace(array('{elapsed_time}', '{memory_usage}'), array($elapsed, $memory), self::$output);
        }

        if (strpos(self::$output, '{exec_time}') !== false or strpos(self::$output, '{mem_usage}') !== false)
        {
            $bm = kali::app_total();
            self::$output = str_replace(
                array('{exec_time}', '{mem_usage}'),
                array(round($bm[0], 4), round($bm[1] / pow(1024, 2), 3)),
                self::$output
            );
        }

        //开启程序分析
        //if (cls_profiler::instance()->enable_profiler === true)
        if (true) 
        {
            $profiler = cls_profiler::instance()->run();
            self::$output = preg_replace('|</body>.*?</html>|is', '', self::$output, -1, $count).$profiler;
            if ($count > 0)
            {
                self::$output .= '</body></html>';
            }
        }

        echo self::$output;

        // 新版本没有介入xhprof
        //if( PHP_SAPI !== 'cli' && !kali::$is_ajax ) 
        //{
            //debug_hanlde_xhprof();
        //}
    }

    public static function exists($tpl)
    {
        return self::$_instance->templateExists($tpl);
    }

    public static function display($tpl)
    {
        return self::fetch($tpl);
    }

    public static function fetch($tpl)
    {
        return self::$output = self::$_instance->fetch($tpl);
    }
}

/* vim: set expandtab: */

