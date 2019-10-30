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
use kaliphp\kali;
use kaliphp\config;
use kaliphp\util;
use kaliphp\db;
use kaliphp\req;
use kaliphp\lib\cls_benchmark;

/**
 * 程序分析类 
 * 
 * @version 2.7.0
 */
class cls_profiler {

    public static $config = [];

    // 当前实例
    protected static $_instance;

    public $enable_profiler = false;

    /**
     * List of profiler sections available to show
     *
     * @var array
     */
    protected $_available_sections = array(
        'benchmarks',
        'get',
        'memory_usage',
        'post',
        'uri_string',
        'controller_info',
        'queries',
        'http_headers',
        'cookie_data',
        'session_data',
        'config'
    );

    /**
     * Number of queries to show before making the additional queries togglable
     *
     * @var int
     */
    protected $_query_toggle_count = 25;

    public static function _init()
    {
        self::$config = config::instance('config')->get('profiler');
    }

    /**
     * Class constructor
     *
     * Initialize Profiler
     *
     * @param	array	$config	Parameters
     */
    public function __construct($config = array())
    {
        // 默认显示所有
        foreach ($this->_available_sections as $section)
        {
            if ( ! isset($config[$section]))
            {
                $var = '_compile_'.$section;
                $this->{$var} = TRUE;
            }
        }

        $this->set_sections($config);
        //log::info('Profiler Class Initialized');
    }

    // --------------------------------------------------------------------

    // 单例模式，生成实例
    public static function instance()
    {
        if (!self::$_instance instanceof self) 
        {
            self::$_instance = new static(self::$config);
        }
        return self::$_instance;
    }

    // --------------------------------------------------------------------

    public function enable_profiler($val = TRUE)
    {
        $this->enable_profiler = is_bool($val) ? $val : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Set Sections
     *
     * Sets the private _compile_* properties to enable/disable Profiler sections
     *
     * @param	mixed	$config
     * @return	void
     */
    public function set_sections($config)
    {
        if (isset($config['query_toggle_count']))
        {
            $this->_query_toggle_count = (int) $config['query_toggle_count'];
            unset($config['query_toggle_count']);
        }

        foreach ($config as $method => $enable)
        {
            if (in_array($method, $this->_available_sections))
            {
                $var = '_compile_'.$method;
                $this->{$var} = ($enable !== FALSE);
            }
        }
    }

    // --------------------------------------------------------------------

    protected function _compile_benchmarks()
    {
        $profile = array();
        foreach (cls_benchmark::$marker as $key => $val)
        {
            if (preg_match('/(.+?)_end$/i', $key, $match)
                && isset(cls_benchmark::$marker[$match[1].'_end'], cls_benchmark::$marker[$match[1].'_start']))
            {
                $profile[$match[1]] = cls_benchmark::elapsed_time($match[1].'_start', $key);
            }
        }

        $output = "\n\n"
            .'<div  id="kali_profiler_benchmarks" style="border:1px solid #900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            .'<legend style="color:#900;">&nbsp;&nbsp;BENCHMARKS&nbsp;&nbsp;</legend>'
            ."\n\n\n<table style=\"width:100%;\">\n";

        foreach ($profile as $key => $val)
        {
            $key = ucwords(str_replace(array('_', '-'), ' ', $key));
            $output .= '<tr><td style="padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;">'
                .$key.'&nbsp;&nbsp;</td><td style="padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;">'
                .$val."</td></tr>\n";
        }

        return $output."</table>\n</div>";
    }

    // --------------------------------------------------------------------

    /**
     * Compile Queries
     *
     * @return	string
     */
    protected function _compile_queries()
    {
        $db_config = config::instance('database')->get();
        // 如果没有启动数据库
        if ( count(db::$queries) == 0 )
        {
            return "\n\n"
                .'<div  id="kali_profiler_queries" style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
                ."\n"
                .'<legend style="color:#0000FF;">&nbsp;&nbsp;QUERIES&nbsp;&nbsp;</legend>'
                ."\n\n\n<table style=\"border:none; width:100%;\">\n"
                .'<tr><td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">'
                .'Database driver is not currently loaded'
                ."</td></tr>\n</table>\n</div>";
        }

        // Key words we want bolded
        $highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', 'AES_ENCRYPT', 'AES_DECRYPT', 'ASC', 'DESC', '(', ')');

        $output  = "\n\n";

        $hide_queries = (count(db::$queries) > $this->_query_toggle_count) ? ' display:none' : '';
        $total_time = number_format(array_sum(db::$query_times), 4).' seconds';

        $show_hide_js = '(<span style="cursor: pointer;" class="kali_profiler_toggle">Hide</span>)';

        if ($hide_queries !== '')
        {
            $show_hide_js = '(<span style="cursor: pointer;" class="kali_profiler_toggle">Show</span>)';
        }

        $output .= '<div  style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            .'<legend style="color:#0000FF;">&nbsp;&nbsp;'
            .'DATABASE:&nbsp; '.$db_config['name'].'&nbsp;&nbsp;&nbsp;'
            .'QUERIES: '.count(db::$queries).' ('.$total_time.')&nbsp;&nbsp;'.$show_hide_js."</legend>\n\n\n"
            .'<table style="width:100%;'.$hide_queries.'" id="kali_profiler_queries_db_1'."\">\n";

        if (count(db::$queries) === 0)
        {
            $output .= '<tr><td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">'
                ."No queries were run</td></tr>\n";
        }
        else
        {
            foreach (db::$queries as $key => $val)
            {
                $time = number_format(db::$query_times[$key], 4);
                $val = util::highlight_code($val);
                // 解决小屏幕无法自动换行bug
                $val = str_replace(
                    array('<code>', '</code>'),
                    array('<div>', '</div>'),
                    $val
                );

                foreach ($highlight as $bold)
                {
                    $val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);
                }

                $output .= '<tr><td style="padding:5px;vertical-align:top;width:1%;color:#900;font-weight:normal;background-color:#ddd;">'
                    .$time.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;font-weight:normal;background-color:#ddd;">'
                    .$val."</td></tr>\n";
            }
        }

        $output .= "</table>\n</div>";

        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Compile $_GET Data
     *
     * @return	string
     */
    protected function _compile_get()
    {
        $output = "\n\n"
            .'<div id="kali_profiler_get" style="border:1px solid #cd6e00;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            ."<legend style=\"color:#cd6e00;\">&nbsp;&nbsp;GET DATA&nbsp;&nbsp;</legend>\n";

        if (count(req::$gets) === 0)
        {
            $output .= '<div style="color:#cd6e00;font-weight:normal;padding:4px 0 4px 0;">No GET data exists</div>';
        }
        else
        {
            $output .= "\n\n<table style=\"width:100%;border:none;\">\n";

            foreach (req::$gets as $key => $val)
            {
                is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, 'utf-8')."'";
                $val = (is_array($val) OR is_object($val))
                    ? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, 'utf-8').'</pre>'
                    : htmlspecialchars($val, ENT_QUOTES, 'utf-8');

                $output .= '<tr><td style="width:50%;color:#000;background-color:#ddd;padding:5px;">&#36;_GET['
                    .$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#cd6e00;font-weight:normal;background-color:#ddd;">'
                    .$val."</td></tr>\n";
            }

            $output .= "</table>\n";
        }

        return $output.'</div>';
    }

    // --------------------------------------------------------------------

    /**
     * Compile $_POST Data
     *
     * @return	string
     */
    protected function _compile_post()
    {
        $output = "\n\n"
            .'<div id="kali_profiler_post" style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            ."<legend style=\"color:#009900;\">&nbsp;&nbsp;POST DATA&nbsp;&nbsp;</legend>\n";

        if (count(req::$posts) === 0 && count(req::$files) === 0)
        {
            $output .= '<div style="color:#009900;font-weight:normal;padding:4px 0 4px 0;">No POST data exists</div>';
        }
        else
        {
            $output .= "\n\n<table style=\"width:100%;\">\n";

            foreach (req::$posts as $key => $val)
            {
                is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, 'utf-8')."'";
                $val = (is_array($val) OR is_object($val))
                    ? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, 'utf-8').'</pre>'
                    : htmlspecialchars($val, ENT_QUOTES, 'utf-8');

                $output .= '<tr><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">&#36;_POST['
                    .$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">'
                    .$val."</td></tr>\n";
            }

            foreach (req::$files as $key => $val)
            {
                is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, 'utf-8')."'";
                $val = (is_array($val) OR is_object($val))
                    ? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, 'utf-8').'</pre>'
                    : htmlspecialchars($val, ENT_QUOTES, 'utf-8');

                $output .= '<tr><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">&#36;_FILES['
                    .$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">'
                    .$val."</td></tr>\n";
            }

            $output .= "</table>\n";
        }

        return $output.'</div>';
    }

    // --------------------------------------------------------------------

    /**
     * Show query string
     *
     * @return	string
     */
    protected function _compile_uri_string()
    {
        return "\n\n"
            .'<div id="kali_profiler_uri_string" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            ."<legend style=\"color:#000;\">&nbsp;&nbsp;URI STRING&nbsp;&nbsp;</legend>\n"
            .'<div style="color:#000;font-weight:normal;padding:4px 0 4px 0;">'
            .($_SERVER['REQUEST_URI'] === '' ? 'No URI data exists' : $_SERVER['REQUEST_URI'])
            .'</div></div>';
    }

    // --------------------------------------------------------------------

    /**
     * Show the controller and function
     *
     * @return	string
     */
    protected function _compile_controller_info()
    {
        if (kali::$ct === '' or kali::$ac === '')
        {
            $msg = "No controller were run";
        }
        else
        {
            $msg = kali::$ct.'/'.kali::$ac;
        }
        return "\n\n"
            .'<div id="kali_profiler_controller_info" style="border:1px solid #995300;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            ."<legend style=\"color:#995300;\">&nbsp;&nbsp;CLASS/METHOD&nbsp;&nbsp;</legend>\n"
            .'<div style="color:#995300;font-weight:normal;padding:4px 0 4px 0;">'.$msg
            .'</div></div>';
    }

    // --------------------------------------------------------------------

    /**
     * Compile memory usage
     *
     * Display total used memory
     *
     * @return	string
     */
    protected function _compile_memory_usage()
    {
        return "\n\n"
            .'<div id="kali_profiler_memory_usage" style="border:1px solid #5a0099;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            ."<legend style=\"color:#5a0099;\">&nbsp;&nbsp;MEMORY USAGE&nbsp;&nbsp;</legend>\n"
            .'<div style="color:#5a0099;font-weight:normal;padding:4px 0 4px 0;">'
            .(($usage = memory_get_usage()) != '' ? number_format($usage).' bytes' : 'Memory Usage Unavailable')
            .'</div></div>';
    }

    // --------------------------------------------------------------------

    /**
     * Compile header information
     *
     * Lists HTTP headers
     *
     * @return	string
     */
    protected function _compile_http_headers()
    {
        $output = "\n\n"
            .'<div  id="kali_profiler_http_headers" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            .'<legend style="color:#000;">&nbsp;&nbsp;HTTP HEADERS'
            .'&nbsp;&nbsp;(<span style="cursor: pointer;"  class="kali_profiler_toggle">Show'."</span>)</legend>\n\n\n"
            .'<table style="width:100%;display:none;" id="kali_profiler_httpheaders_table">'."\n";

        foreach (array('HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR', 'HTTP_DNT') as $header)
        {
            $val = isset($_SERVER[$header]) ? htmlspecialchars($_SERVER[$header], ENT_QUOTES, 'utf-8') : '';
            $output .= '<tr><td style="vertical-align:top;width:50%;padding:5px;color:#900;background-color:#ddd;">'
                .$header.'&nbsp;&nbsp;</td><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">'.$val."</td></tr>\n";
        }

        return $output."</table>\n</div>";
    }

    // --------------------------------------------------------------------

    /**
     * Compile config information
     *
     * Lists developer config variables
     *
     * @return	string
     */
    protected function _compile_config()
    {
        $output = "\n\n"
            .'<div  id="kali_profiler_config" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            ."\n"
            .'<legend style="color:#000;">&nbsp;&nbsp;CONFIG VARIABLES&nbsp;&nbsp;(<span style="cursor: pointer;" class="kali_profiler_toggle">Show'."</span>)</legend>\n\n\n"
            .'<table style="width:100%;display:none;" id="kali_profiler_config_table">'."\n";

        //foreach (config::$call_configs as $config => $val)
        //{
        //if (is_array($val) OR is_object($val))
        //{
        //$val = print_r($val, TRUE);
        //}

        //$output .= '<tr><td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">'
        //.$config.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">'.htmlspecialchars($val, ENT_QUOTES, 'utf-8')."</td></tr>\n";
        //}

        return $output."</table>\n</div>";
    }

    // --------------------------------------------------------------------

    /**
     * Compile session userdata
     *
     * @return 	string
     */
    protected function _compile_cookie_data()
    {
        if ( ! isset(req::$cookies))
        {
            return;
        }

        $output = '<div  id="kali_profiler_cookie" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            .'<legend style="color:#000;">&nbsp;&nbsp;COOKIE DATA&nbsp;&nbsp;(<span style="cursor: pointer;"  class="kali_profiler_toggle">Show</span>)</legend>'
            .'<table style="width:100%;display:none;" id="kali_profiler_cookie_data">';

        foreach (req::$cookies as $key => $val)
        {
            if (is_array($val) OR is_object($val))
            {
                $val = print_r($val, TRUE);
            }

            $output .= '<tr><td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">'
                .$key.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">'.htmlspecialchars($val, ENT_QUOTES, 'utf-8')."</td></tr>\n";
        }

        return $output."</table>\n</div>";
    }

    // --------------------------------------------------------------------

    /**
     * Compile session userdata
     *
     * @return 	string
     */
    protected function _compile_session_data()
    {
        if ( ! isset($_SESSION))
        {
            return;
        }

        $output = '<div   id="kali_profiler_session" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
            .'<legend style="color:#000;">&nbsp;&nbsp;SESSION DATA&nbsp;&nbsp;(<span style="cursor: pointer;"  class="kali_profiler_toggle">Show</span>)</legend>'
            .'<table style="width:100%;display:none;" id="kali_profiler_session_data">';

        foreach ($_SESSION as $key => $val)
        {
            if (is_array($val) OR is_object($val))
            {
                $val = print_r($val, TRUE);
            }

            $output .= '<tr><td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">'
                .$key.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">'.htmlspecialchars($val, ENT_QUOTES, 'utf-8')."</td></tr>\n";
        }

        return $output."</table>\n</div>";
    }

    // --------------------------------------------------------------------

    /**
     * Run the Profiler
     *
     * @return	string
     */
    public function run()
    {
        $output = '<style>
            #kali_profiler{ clear:both; padding:0 20px !important; margin-bottom:15px; }
            #kali_profiler>div{ background: #fff; padding:20px 15px; }
            #kali_profiler>div tr td div{ word-break: break-all; word-wrap: break-word; background: #fff; padding:10px; }
            #kali_profiler table{ display: table; border-collapse: separate; border-spacing: 2px; border-color: grey; background: none; background-color: none; }
            #kali_profiler div{ overflow-x: scroll; }
            </style>';
        $output .= '<div id="kali_profiler"><div>';
        $fields_displayed = 0;

        foreach ($this->_available_sections as $section)
        {
            $var = '_compile_'.$section;
            if ($this->{$var} !== FALSE)
            {
                $output .= $this->{$var}();
                $fields_displayed++;
            }
        }

        if ($fields_displayed === 0)
        {
            $output .= '<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee;">'
                .'No Profile data - all Profiler sections have been disabled.</p>';
        }

        $output .= '</div></div>';
        $output .= '
            <script>
                $(".kali_profiler_toggle").on("click",function(){

                    var txt = $(this).text();
                    if(txt=="Hide"){
                        $(this).text("Show");

                    }else{
                        $(this).text("Hide");
                    }
                    $(this).closest("div").find("table").toggle();
                })
            </script>
        ';
        return $output;
    }

}

