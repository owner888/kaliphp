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

// 解决单独使用时找不到定义问题
defined('CRYPT_KEY') or define('CRYPT_KEY', (string) ($_ENV['CRYPT_KEY'] ?? ''));

use kaliphp\lib\cls_cli;
use kaliphp\lib\cls_arr;
use kaliphp\lib\cls_crypt;
use kaliphp\lib\cls_filter;
use kaliphp\lib\cls_security;
use kaliphp\lib\cls_ip2location;

/**
 * 处理外部请求变量的类
 *
 * 禁止此文件以外的文件出现 $_POST、$_GET、$_FILES 变量 以及 eval函数(用 req::myeval )
 * 以便于对主要黑客攻击进行防范
 *
 * @version 3.0
 */
class req
{
    public static $config = [];

    // $_COOKIE 变量
    public static $cookies = [];

    // $_SESSION 变量
    public static $sessions = [];

    // Returns all of the GET, POST array's，like $_REQUEST
    public static $forms = [];

    // $_GET 变量
    public static $gets = [];

    // $_POST 变量
    public static $posts = [];

    // 文件变量
    public static $files = [];

    // 不允许保存的文件
    public static $filter_filename = '/\.(php|pl|sh|js)$/i';

    /**
     * 过滤器是否抛出异常
     * (只对邮箱、用户名、qq、手机类型有效)
     * 如果不抛出异常，对无效的数据修改为空字符串
     */
    public static $throw_error = false;

    /**
     * 初始化用户请求
     * 对于 post、get 的数据，会转到 selfforms 数组，并删除原来数组
     * 对于 cookie 的数据，会转到 cookies 数组，但不删除原来数组
     * 对于 session 的数据，只做 XSS 过滤处理
     * 本方法内不允许抛出异常，因为 errorhandler.php 里面调用了当前类，会进入死循环
     */
    public static function _init()
    {
        self::$config = config::instance('config')->get('request');
        // 匹配全局输入数据 $_SESSION、$_COOKIE、$_POST、$_GET ...
        self::hydrate();
    }

    public static function get_use_compress(): bool   
    {
        $value = self::server('HTTP_ACCEPT_ENCODING', '');

        return (bool) strstr($value, 'gzip');
    }

    public static function get_use_base64(): bool
    {
        $value = self::server('HTTP_ACCEPT_BASE64', '');

        return $value == '1';
    }

    public static function get_use_encrypt(): bool
    {
        $value = self::server('HTTP_ACCEPT_ENCRYPT', '');

        return $value == '1';
    }

    public static function get_encrypt_key(): string
    {
        return CRYPT_KEY;
    }

    /**
     * 对gpc变量进行转义处理
     * 
     * @param mixed $str str 
     * 
     * @return string|array
     */
    public static function add_s($str)
    {
        // Is the string an array?
        if (is_array($str))
        {
            foreach ($str as $key => $value)
            {
                $str[$key] = self::add_s($value);
            }

            return $str;
        }

        if ( $str && is_string($str) ) 
        {
            $str = addslashes($str);
        }

        return $str;
    }

    /**
     * 把 eval 重命名为 myeval
     */
    public static function myeval( $phpcode )
    {
        return eval( $phpcode );
    }

    /**
     * Returns PHP's raw input
     *
     * @return  string
     */
    public static function raw(): string
    {
        // get php raw input
        return (string) file_get_contents('php://input');
    }

    /**
     * Returns all of the GET, POST, PUT, PATCH or DELETE array's
     *
     * @return  array
     */
    public static function all()
    {
        return array_merge(self::$gets, self::$posts);
    }

    /**
     * Gets the specified GET、POST variable.
     *
     * @param   string  $index    The index to get
     * @param   string  $default  The default value
     * @return  string|array
     */
    public static function item( $index = null, $default = null, $filter_type = '' )
    {   
        $value = (func_num_args() === 0) ? self::all() : cls_arr::get(self::all(), $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
    }

    /**
     * Gets the specified GET variable.
     *
     * @param   string  $index    The index to get
     * @param   string  $default  The default value
     * @return  string|array
     */
    public static function get( $index = null, $default = null, $filter_type = '' )
    {   
        $value = (func_num_args() === 0) ? self::$gets : cls_arr::get(self::$gets, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
    }

    /**
     * Gets the specified POST variable.
     *
     * @param   string  $index    The index to get
     * @param   string  $default  The default value
     * @return  string|array
     */
    public static function post( $index = null, $default = null, $filter_type = '' )
    {   
        $value = (func_num_args() === 0) ? self::$posts : cls_arr::get(self::$posts, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
    }

    /**
     * 获得指定cookie值
     */
    public static function cookie( $index = null, $default = null, $filter_type = '' )
    {
        $value = (func_num_args() === 0) ? self::$cookies : cls_arr::get(self::$cookies, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
    }

    /**
     * 获得SERVER值
     *
     * @param   string  $index    索引
     * @param   mixed   $default  默认值
     * @return  string|array
     */
    public static function server( $index = null, $default = null )
    {
        return (func_num_args() === 0) ? $_SERVER : cls_arr::get($_SERVER, strtoupper($index), $default);
    }

    /**
     * Fetch a item from the HTTP request headers
     *
     * @param   mixed $index
     * @param   mixed $default
     * @return  mixed
     */
    public static function headers( $index = null, $default = null )
    {
        static $headers = null;

        // do we need to fetch the headers?
        if ( $headers === null )
        {
            // deal with fcgi or nginx installs
            if ( ! function_exists('getallheaders'))
            {
                $server = cls_arr::filter_prefixed(static::server(), 'HTTP_', true);

                foreach ($server as $key => $value)
                {
                    $key = join('-', array_map('ucfirst', explode('_', strtolower($key))));

                    $headers[$key] = $value;
                }

                $value = static::server('Content_Type') and $headers['Content-Type'] = $value;
                $value = static::server('Content_Length') and $headers['Content-Length'] = $value;
            }
            else
            {
                $headers = getallheaders();
            }
        }

        return empty($headers) ? $default : ((func_num_args() === 0) ? $headers : cls_arr::get(array_change_key_case($headers), strtolower($index), $default));
    }

    public static function language()
    {
        if ($lang = self::cookie("language"))
        {
            return $lang;
        }

        $languages = [];
        if ( !empty(self::server('HTTP_ACCEPT_LANGUAGE')) )
        {
            $languages = explode(',', preg_replace('/(;\s?q=[0-9\.]+)|\s/i', '', strtolower(trim(self::server('HTTP_ACCEPT_LANGUAGE')))));
        }

        if (count($languages) === 0)
        {
            $languages = array('Undefined');
        }

        $lang = !in_array($languages[0], array("zh-cn", "zh-tw", "en")) ? "zh-cn" : $languages[0];
        return $lang;
    }

    /**
     * Return's the query string
     *
     * @param   string $default
     * @return  string
     */
    public static function query_string($default = '')
    {
        return static::server('QUERY_STRING', $default);
    }

    /**
     * Return's the host
     *
     * @param   string $default
     * @return  string
     */
    public static function host($default = '')
    {
        return static::server('HTTP_HOST', $default);
    }

    /**
     * Return's the port
     *
     * @param   string $default
     * @return  string
     */
    public static function port($default = '')
    {
        return static::server('SERVER_PORT', $default);
    }

    /**
     * Return's the remote port
     *
     * @param   string $default
     * @return  string
     */
    public static function remote_port($default = '')
    {
        return static::server('REMOTE_PORT', $default);
    }

    /**
     * 返回请求所使用的协议
     *
     * @return  string  http | https
     */
    public static function protocol()
    {
        if (static::server('HTTPS') == 'on' or
            static::server('HTTPS') == 1 or
            static::server('SERVER_PORT') == 443 or
            static::server('HTTP_X_FORWARDED_PROTO') == 'https' or
            static::server('HTTP_X_FORWARDED_PORT') == 443)
        {
            return 'https';
        }

        return 'http';
    }

     /**
     * 获取当前包含协议的域名
     *
     * exp:
     * https://www.kaliphp.com
     */
    public static function domain()
    {
        return self::protocol() . '://' . self::host();
    }

    /**
     * 获得当前网址，不包含 Query String
     *
     * exp:
     * https://www.kaliphp.com/home/
     */
    public static function base_url($uri = '')
    {
        $url = self::url();
        // 排除 Query String 后的 URL
        $url = strpos($url, '?') ? strstr($url, '?', true) : $url;
        return $uri ? self::domain() . $uri : $url;
    }

    /**
     * 获得当前完整网址，包含 Query String
     * 
     * @return string
     */
    public static function url()
    {
        return self::domain().'?'.self::query_string();    
    }

    public static function path()
    {
        return self::cururl();
    }

    /**
     * 获得当前路径
     * /index.php?ct=test&ac=demo&id=10
     */
    public static function cururl()
    {
        if(!empty(self::server("REQUEST_URI")))
        {
            $script_name = self::server("REQUEST_URI");
            $nowurl = $script_name;
        }
        else
        {
            $script_name = self::server("PHP_SELF");
            $nowurl = empty(self::server("QUERY_STRING")) ? $script_name : $script_name."?".self::server("QUERY_STRING");
        }
        return $nowurl;
    }

    /**
     * Return's the referrer
     *
     * @param   string $default
     * @return  string
     */
    public static function referrer($default = '')
    {
        return static::server('HTTP_REFERER', $default);
    }

    /**
     * 获取用户的公共IP地址
     *
     * @param   string $default
     * @return  array|string
     */
    public static function ip($default = '0.0.0.0')
    {
        return static::server('REMOTE_ADDR', $default);
    }

    /**
     * 获得国家中文名
     * 
     * @param string $ip
     * @return string
     */
    public static function country_cn($ip = '')
    {
        $tmp = config::instance('areacode')->get();
        $map = array_column($tmp, null, 2); // 以第 2 个作为 key
        return $map[self::country($ip)][0] ?? '-';
    }

    /**
     * 获得国家代码
     * 
     * @param string $ip
     * @return string
     */
    public static function country($ip = '')
    {
        // 如果是通过IP来获取城市地址的
        if (!empty($ip)) 
        {
            if (!file_exists(APPPATH.'/../../IP-COUNTRY-ISP.BIN')) 
            {
                return "-";
            }
            $db = new cls_ip2location(APPPATH.'/../../IP-COUNTRY-ISP.BIN', cls_ip2location::FILE_IO);
            $records = $db->lookup($ip, [cls_ip2location::COUNTRY_CODE]);
            return strtoupper($records['countryCode']);
        }

        // 优先获取客户端的传值
        $country = self::item("country");
        if ( !empty($country)) 
        {
            return $country;
        }

        //域名上了接入层获取方法
        if( !empty(self::server('HTTP_X_REAL_COUNTRY_SHORT')) )
        {
            return self::server('HTTP_X_REAL_COUNTRY_SHORT');
        }
        // 如果nginx加载了ip2location模块，直接获取参数
        else if ( !empty(self::server('COUNTRY_SHORT'))) 
        {
            return self::server('COUNTRY_SHORT');
        }
        // 通过PHP类库获取
        else 
        {
            // /data/web 目录
            if (!file_exists(APPPATH.'/../../IP-COUNTRY-ISP.BIN')) 
            {
                return "-";
            }
            $ip = self::ip();
            $db = new cls_ip2location(APPPATH.'/../../IP-COUNTRY-ISP.BIN', cls_ip2location::FILE_IO);
            $records = $db->lookup($ip, [cls_ip2location::COUNTRY_CODE]);
            return strtoupper($records['countryCode']);
        }
    }

    /**
     * Return's the user agent
     *
     * @param   $default
     * @return  string
     */
    public static function user_agent($default = '')
    {
        return static::server('HTTP_USER_AGENT', $default);
    }

    /**
     * Return's the user browser
     */
    public static function browser()
    {
        $br = self::user_agent();
        if ( !empty($br)) 
        {
            if (preg_match('/MSIE/i', $br)) 
            {
                $br = 'MSIE';
            } 
            else if (preg_match('/Firefox/i', $br)) 
            {
                $br = 'Firefox';
            } 
            else if (preg_match('/Chrome/i', $br)) 
            {
                $br = 'Chrome';
            } 
            else if (preg_match('/Safari/i', $br)) 
            {
                $br = 'Safari';
            } 
            else if (preg_match('/Opera/i', $br)) 
            {
                $br = 'Opera';
            } 
            else 
            {
                $br = 'Other';
            }
            return $br;
        } 
        else 
        {
            return 'Unknow';
        }
    }

    /**
     * Return's the user os
     */
    public static function os()
    {
        $user_agent = self::user_agent();
        if ( !empty($user_agent)) 
        {
            $mua = [
                'iOS'     => '#iphone|ipad|ios#i',
                'Android' => '#android|\s+adr\s+#i',
                'Windows' => '#win#i',
                'MacOS'   => '#mac#i',
                'Linux'   => '#linux#i',
                'Unix'    => '#unix#i',
                'BSD'     => '#bsd#i',
            ];

            $platform = 'Other';
            foreach($mua as $plf => $regex)
            {
                if(preg_match($regex, $user_agent))
                {
                    $platform = $plf;
                    break;
                }
            }
            return $platform;
        }
        else 
        {
            return 'Unknow';
        }
    }

    // 是否请求中文数据
    public static function is_cn()
    {
        if (self::server('HTTP_ACCEPT_LANGUAGE') == 'zh-cn') 
        {
            return true;
        }

        return false;
    }

    /**
     * 客户端是否请求JSON编码内容 
     * Accept 属于请求头，表示客户端希望接收到的数据内容
     * Content-Type 属于实体头，表示客户端发送到服务端的数据内容
     *
     * @return	bool
     */
    public static function is_json(): bool
    {
        if (self::item('is_json', 0, 'int'))
        {
            return true;
        }

        if (self::server('HTTP_CONTENT_TYPE') == 'application/json') 
        {
            return true;
        }

        $http_accept = (string) self::server('HTTP_ACCEPT');
        $http_accepts = explode(';', $http_accept);
        $http_accept = $http_accepts[0];
        $http_accepts = explode(',', $http_accept);
        return in_array('application/json', $http_accepts);
    }

    /**
     * 检查运行环境是否命令行
     *
     * @return	bool
     */
    public static function is_cli()
    {
        return self::method() === 'CLI';
    }

    /**
     * 检查运行环境是否终端
     *
     * @return	bool
     */
    public static function is_terminal()
    {
        return defined("STDERR") && is_resource(STDERR) && function_exists('posix_isatty') && posix_isatty(STDERR);    
    }

    /**
     * jquery 发出 ajax 请求时，会在请求头部添加一个名为X-Requested-With的信息，信息内容为 XMLHttpRequest
     *
     * js 需要如下处理
     * var xmlhttp = new XMLHttpRequest(); 
     * xmlhttp.open("GET", "test.php", true); 
     * xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest"); 
     * xmlhttp.send();
     * 
     * @return	bool
     */
    public static function is_ajax()
    {
        return (static::server('HTTP_X_REQUESTED_WITH') !== null) and strtolower(static::server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    public static function is_html5()
    {
        $rs = true;
        if(!empty(self::server('HTTP_USER_AGENT')) && strpos(self::server('HTTP_USER_AGENT'), "MSIE")) 
        {
            preg_match("#msie (\d+)#i", self::server('HTTP_USER_AGENT'), $out);
            $version = empty($out[1]) ? 10 : intval($out[1]);
            if ($version < 9) 
            {
                $rs = false;
            }
        }
        return $rs;
    }

    /**
     * 通关ua判断是否为手机
     *
     * @return bool
     */
    public static function is_mobile()
    {
        //正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320×320|240×320|176×220";
        $regex_match .= "|mqqbrowser|juc|iuc|ios|ipad";
        $regex_match .= ")/i";

        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower(static::server('HTTP_USER_AGENT')));
    }

    /**
     * 获取请求方法 POST、GET、PUT、DELETE、HEAD、OPTIONS
     * 
     * @return string
     */
    public static function method()
    {
        if ( PHP_SAPI === 'cli' ) 
        {
            return strtoupper(PHP_SAPI);
        }
        elseif ( null !== self::server('HTTP_X_HTTP_METHOD_OVERRIDE') ) 
        {
            return strtoupper(self::server('HTTP_X_HTTP_METHOD_OVERRIDE'));
        }
        else 
        {
            return null !== self::server('REQUEST_METHOD') ? 
                strtoupper(self::server('REQUEST_METHOD')) : 'GET';
        }
    }

    /**
     * 前页
     * 
     * @param mixed $url
     * @return void
     */
    public static function forword($gourl = '')
    {
        $gourl = empty(self::server('HTTP_REFERER')) ? $gourl : self::server('HTTP_REFERER');
        return $gourl;
    }

    /**
     * 设置跳转页
     * 
     * @param string $gourl
     *
     * @return void
     */
    public static function set_redirect($gourl = '')
    {
        $gourl = urlencode($gourl);
        setcookie('gourl', $gourl);
    }

    /**
     * 获取跳转页
     * 
     * @param string $gourl
     *
     * @return string
     */
    public static function redirect($gourl = '')
    {
        $gourl = self::cookie('gourl', $gourl, 'urldecode');
        $gourl = $gourl ?: self::referrer();
        return $gourl;
    }

    /**
     * 过滤文件相关
     */
    public static function filter_files( &$files )
    {
        self::$files = $files;
        unset($_FILES);
    }

    /**
     * 移动上传的文件
     * $item 是用于当文件表单名为数组，如 upfile[] 之类的情况, $item 表示数组的具体键值，下同
     *
     * @return bool
     */
    public static function move_upload_file( $formname, $filename, $item = '' )
    {
        if( self::is_upload_file( $formname, $item ) )
        {
            if( preg_match(self::$filter_filename, $filename) )
            {
                return false;
            }
            else
            {
                if( $item === '' ) 
                {
                    if( PHP_OS == 'WINNT')
                    {
                        return copy(self::$files[$formname]['tmp_name'], $filename);
                    }
                    else
                    {
                        return move_uploaded_file(self::$files[$formname]['tmp_name'], $filename);
                    }
                }
                else 
                {
                    if( PHP_OS == 'WINNT')
                    {

                        return copy(self::$files[$formname]['tmp_name'][$item], $filename);
                    }
                    else 
                    {
                        return move_uploaded_file(self::$files[$formname]['tmp_name'][$item], $filename);
                    }
                }
            }
        }
    }

    /**
     * 获得指定临时文件名值
     */
    public static function get_tmp_name( $formname, $defaultvalue = '', $item = '' )
    {
        if( $item === '' ) 
        {
            return isset(self::$files[$formname]['tmp_name']) ? self::$files[$formname]['tmp_name'] :  $defaultvalue;
        }
        else
        {
            return isset(self::$files[$formname]['tmp_name'][$item]) ? self::$files[$formname]['tmp_name'][$item] :  $defaultvalue;
        }
    }

    /**
     * 获得文件的扩展名
     */
    public static function get_shortname( $formname, $item = '' )
    {
        if( $item === '' ) 
        {
            $filetype = strtolower(isset(self::$files[$formname]['type']) ? self::$files[$formname]['type'] : '');
        }
        else 
        {
            $filetype = strtolower(isset(self::$files[$formname]['type'][$item]) ? self::$files[$formname]['type'][$item] : '');
        }

        $shortname = '';
        switch($filetype)
        {
        case 'image/jpeg':
            $shortname = 'jpg';
            break;
        case 'image/pjpeg':
            $shortname = 'jpg';
            break;
        case 'image/gif':
            $shortname = 'gif';
            break;
        case 'image/png':
            $shortname = 'png';
            break;
        case 'image/xpng':
            $shortname = 'png';
            break;
        case 'image/wbmp':
            $shortname = 'bmp';
            break;
        default:
            if( $item === '' )
            {
                $filename = isset(self::$files[$formname]['name']) ? self::$files[$formname]['name'] : '';
            } 
            else
            {
                $filename = isset(self::$files[$formname]['name'][$item]) ? self::$files[$formname]['name'][$item] : '';
            }
            if( preg_match("/\./", $filename) )
            {
                $fs = explode('.', $filename);
                $shortname = strtolower($fs[ count($fs)-1 ]);
            }
            break;
        }
        return $shortname;
    }

    /**
     * 获得指定文件表单的文件详细信息
     */
    public static function get_file_info( $formname, $item = '' )
    {
        if( !isset( self::$files[$formname] ) )
        {
            return false;
        }
        else
        {
            if($item === '')
            {
                return self::$files[$formname];
            }
            else
            {
                if( !isset(self::$files[$formname][$item]) ) 
                {
                    return false;
                }
                else
                {
                    return self::$files[$formname][$item];
                }
            }
        }
    }

    /**
     * 判断是否存在上传的文件
     */
    public static function is_upload_file( $formname,  $item = '' )
    {
        if( $item === '' ) 
        {
            if( isset(self::$files[$formname]['error']) && self::$files[$formname]['error']==UPLOAD_ERR_OK  ) 
            {
                return true;
            }
            else 
            {
                return false;
            }
        }
        else 
        {
            if( isset(self::$files[$formname]['error'][$item]) && self::$files[$formname]['error'][$item]==UPLOAD_ERR_OK  ) 
            {
                return true;
            } 
            else 
            {
                return false;
            }
        }
    }

    /**
     * 检查文件后缀是否为指定值
     *
     * @param	string	$formname
     * @param	array	$subfix
     * @param	string	$item
     * @return	bool
     */
    public static function check_subfix(string $formname, array $subfix = [], string $item = ''): bool
    {
        if( !in_array(self::get_shortname($formname, $item), $subfix))
        {
            return false;
        }
        return true;
    }

    /**
     * 把指定数据转化为路由数据
     *
     * @param  $data    数据列表 [ key => value, ... ] )
     * @param  $method  方法
     *
     * @return bool
     */
    public static function assign_values(array $data, $method = 'GET')
    {
        foreach($data as $k => $v)
        {
            self::$forms[$k] = $v;

            // 给值 gets/posts
            if( strtoupper($method) == 'GET' ) 
            {
                self::$gets[$k] = $v;
            }
            else 
            {
                self::$posts[$k] = $v;
            }
        }
    }

    /**
     * Hydrates the input array
     *
     * @return  void
     */
    protected static function hydrate()
    {
        // get the input method and unify it
        $method = strtolower(self::method());

        // get the content type from the header, strip optional parameters
        $content_header = self::headers('Content-Type', '');
        if (($content_type = strstr($content_header, ';', true)) === false)
        {
            $content_type = $content_header;
        }

        // fetch the raw input data
        $php_input = self::raw();

        // var_dump(self::get_encrypt_key(), self::get_use_encrypt(), self::get_use_base64(), self::get_use_compress()); exit;

        // 是否开启加密，客户端要求加密 或者 配置强制加密
        if ( self::get_use_encrypt() ) 
        {
            if ( empty(self::get_encrypt_key()) ) 
            {
                exit('Encrypt key undefined');
            }

            $php_input = cls_crypt::decode(
                $php_input, 
                self::get_encrypt_key(), 
                self::get_use_base64()
            );

            if (self::get_use_compress())
            {
                if (@gzinflate($php_input) === false)
                {
                    resp::response(-1, [], 'gzinflate error');
                }
                else
                {
                    $php_input = gzinflate($php_input);
                }
            }
            log::debug($php_input,'test');
            // var_dump($php_input);exit;

            $data = (array) json_decode($php_input, true);

            if ( json_last_error() != JSON_ERROR_NONE )
            {
                resp::response(-1, [], 'decrypt error: ' . json_last_error());
            }

            // 清空请求数据
            $php_input = '';
            $_GET = $_POST = $_REQUEST = [];

            // 覆盖 $_POST 值
            self::assign_values($data, 'POST');
        }
        // handle application/x-www-form-urlencoded input
        else if ( $content_type == 'application/x-www-form-urlencoded' )
        {
            // double-check if max_input_vars is not exceeded,
            // it doesn't always give an E_WARNING it seems...
            if ( $method == 'get' or $method == 'post' )
            {
                if ($php_input and ($amps = substr_count($php_input, '&')) > ini_get('max_input_vars'))
                {
                    throw new \Exception('Input truncated by PHP. Number of variables exceeded '.ini_get('max_input_vars').'. To increase the limit to at least the '.$amps.' needed for this HTTP request, change the value of "max_input_vars" in php.ini.');
                }
            }
            else
            {
                $php_input = urldecode($php_input);
                // other methods than GET and POST need to be parsed manually
                parse_str($php_input, $php_input);
            }
        }
        // handle multipart/form-data input
        elseif ( $content_type == 'multipart/form-data' )
        {
            // grab multipart boundary from content type header
            preg_match('/boundary=(.*)$/', $content_header, $matches);
            $boundary = $matches[1];

            // split content by boundary and get rid of last -- element
            $blocks = preg_split('/-+'.$boundary.'/', $php_input);
            array_pop($blocks);

            // loop data blocks
            $php_input = [];
            foreach ($blocks as $block)
            {
                // skip empty blocks
                if ( ! empty($block))
                {
                    // parse uploaded files
                    if (strpos($block, 'application/octet-stream') !== FALSE)
                    {
                        // match "name", then everything after "stream" (optional) except for prepending newlines
                        preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
                    }
                    // parse all other fields
                    else
                    {
                        // match "name" and optional value in between newline sequences
                        preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
                    }

                    // store the result, if any
                    $php_input[$matches[1]] = isset($matches[2]) ? $matches[2] : '';
                }
            }
        }
        // handle json input
        elseif ($content_type == 'application/json')
        {
            if (util::is_json($php_input)) 
            {
                $php_input = json_decode($php_input, true);
            }
            else 
            {
                $php_input = urldecode($php_input);
                parse_str($php_input, $php_input);
            }
 
            self::${$method.'s'} = $php_input;
        }
        // handle xml input
        elseif ($content_type == 'application/xml' or $content_type == 'text/xml')
        {
            $php_input = self::xml_to_array(new \SimpleXMLElement($php_input));
            self::${$method.'s'} = $php_input;
        }
        // unknown input format
        elseif ($php_input and ! is_array($php_input))
        {
            // don't know how to handle it, allow the application to handle it
            // reset the method to avoid having it stored below!
            $method = null;
        }

        // 命令行模式
        if( $method === 'cli' ) 
        {
            // 把命令行参数转化为get参数
            if ( count(cls_cli::$args) > 0) 
            {
                foreach (cls_cli::$args as $k=>$v) 
                {
                    if (!is_numeric($k)) 
                    {
                        $_GET[$k] = $v;
                    }
                }
            }
        }

        // 上传的文件处理
        if( isset($_FILES) && count($_FILES) > 0 )
        {
            self::filter_files($_FILES);
        }

        // 处理 get
        if( count($_GET) > 0 )
        {
            self::$gets = $_GET;
        }
     
        // 处理 post
        if( count($_POST) > 0 )
        {
            self::$posts = $_POST;
        }

        // 处理 cookie
        if( count($_COOKIE) > 0 )
        {
            self::$cookies = $_COOKIE;
        }

        // 处理 request
        if( self::$gets || self::$posts )
        {
            // 修改成 gets 和 posts 的集合，更适合一点
            self::$forms = array_merge(self::$gets, self::$posts);
        }
    
        $_GET = $_POST = $_REQUEST = [];

        // store the parsed data based on the request method
        if ( $php_input && ($method == 'put' or $method == 'patch' or $method == 'delete') )
        {
            self::${$method.'s'} = !is_array($php_input) ? json_decode($php_input, true) : $php_input;
        }
 
        // 开启过滤
        if ( !ini_get('magic_quotes_gpc') && !empty(self::$config['use_magic_quotes']) ) 
        {
            foreach(['forms', 'gets', 'posts'] as $f)
            {
                if( self::${$f} )
                {
                    self::${$f} = self::add_s(self::${$f});
                }
            }
        }

        // 开启过滤
        if ( self::$config['global_xss_filtering'] ) 
        {
            foreach(['forms', 'gets', 'posts'] as $f)
            {
                if( self::${$f} )
                {
                    self::${$f} = cls_security::xss_clean(self::${$f});
                }
            }
        }
    }

    public static function xml_to_array($xmls) 
    {
        $array = [];

        foreach ($xmls as $key => $xml) 
        {
            $count = $xml->count();

            if ($count == 0) 
            {
                $res = (string) $xml;
            } 
            else 
            {
                $res = self::xml_to_array($xml);
            }

            $array[$key] = $res;
        }

        return $array;
    }
}

/* vim: set expandtab: */

