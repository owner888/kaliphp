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
            require_once kali::$base_root.'/core/lib/smarty/Smarty.class.php';
            self::$_instance = new \Smarty();
            self::$_instance->setTemplateDir(kali::$view_root);
            self::$_instance->setCompileDir(util::path_exists( kali::$cache_root.DS.'template_'.kali::$app_name.DS.'compile' ));
            self::$_instance->setCacheDir(util::path_exists( kali::$cache_root.DS.'template_'.kali::$app_name.DS.'compile' ));
            self::$_instance->addPluginsDir(kali::$base_root.'/core/lib/smarty_plugins');

            self::$_instance->setLeftDelimiter('<{');
            self::$_instance->setRightDelimiter('}>');
            self::$_instance->setCompileCheck(true);
            //self::$_instance->force_compile = true;
            //self::$_instance->debugging = true;
            //self::$_instance->caching = true;
            //self::$_instance->cache_lifetime = 120;
            //self::$_instance->load_filter('output', 'gzip');
            self::config();
        }

        // html都有title，这里直接赋值好了
        self::assign('title', kali::$app_title);

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
        if (cls_profiler::instance()->enable_profiler === true)
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

