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
use kaliphp\lib\cls_arr;
use kaliphp\lib\cls_security;
use kaliphp\lib\cls_filter;
use kaliphp\lib\cls_cli;
use kaliphp\lib\cls_ip2location;

/**
 * 处理外部请求变量的类
 *
 * 禁止此文件以外的文件出现 $_POST、$_GET、$_FILES变量及eval函数(用 req::myeval )
 * 以便于对主要黑客攻击进行防范
 *
 * @author seatle<seatle@foxmail.com>
 * @version 2.0
 */
class req
{
    public static $config = [];

    /**
     * @var  string  $raw  raw PHP input
     */
    protected static $raw_input = null;

    // $_COOKIE 变量
    public static $cookies = array();

    // Returns all of the GET, POST, PUT, PATCH or DELETE array's，like $_REQUEST
    public static $forms = array();

    // $_GET 变量
    public static $gets = array();

    // $_POST 变量
    public static $posts = array();

    // All PUT input
    public static $puts = array();

    // All DELETE input
    public static $deletes = array();

    // All PATCH input
    public static $patchs = array();

	// parsed request body as json
    public static $jsons = array();

    // parsed request body as xml
    public static $xmls = array();

    // 文件变量
    public static $files = array();

    // 这个可以强制设置
    public static $is_force_ajax = false;

    // url_rewrite
    public static $url_rewrite = false;

    // 严禁保存的文件名
    public static $filter_filename = '/\.(php|pl|sh|js)$/i';

    /**
     * 过滤器是否抛出异常
     * (只对邮箱、用户名、qq、手机类型有效)
     * 如果不抛出异常，对无效的数据修改为空字符串
     */
    public static $throw_error = false;

    /**
     * 初始化用户请求
     * 对于 post、get 的数据，会转到 selfforms 数组， 并删除原来数组
     * 对于 cookie 的数据，会转到 cookies 数组，但不删除原来数组
     * 本方法内不允许抛出异常，因为errorhandler.php里面调用了当前类，会进入死循环
     */
    public static function _init()
    {
        self::$config = config::instance('config')->get('request');

		// get php raw input
        self::$raw_input = file_get_contents('php://input');

        // fetch global input data
        self::hydrate();

        //默认ac和ct
        self::$forms['ct'] = isset(self::$forms['ct']) ? self::$forms['ct'] : 'index';
        self::$forms['ac'] = isset(self::$forms['ac']) ? self::$forms['ac'] : 'index';
    }

    //强制要求对gpc变量进行转义处理
    public static function add_s( $str )
    {
        // Is the string an array?
        if (is_array($str))
        {
            foreach ($str as $key => &$value)
            {
                $str[$key] = self::add_s($value);
            }

            return $str;
        }

        $str = addslashes($str);
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
	 * @return  array
	 */
	public static function raw()
	{
		return self::$raw_input;
	}

	/**
	 * Returns all of the GET, POST, PUT, PATCH or DELETE array's
	 *
	 * @return  array
	 */
	public static function all()
	{
		return array_merge(self::$gets, self::$posts, self::$puts, self::$patchs, self::$deletes);
	}

    /**
     * 获得指定表单值
     * 
     * @param mixed $formname       表单名
     * @param string $defaultvalue  默认值
     * @param string $formattype    格式化类型
     * @return mixed $return        返回值
     * @author seatle <seatle@foxmail.com> 
     * @created time :2014-12-16 10:48
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
	 * Fetch an item from the php://input for put arguments
	 *
	 * @param   string  $index    The index key
	 * @param   mixed   $default  The default value
	 * @return  string|array
	 */
	public static function put( $index = null, $default = null, $filter_type = '' )
	{
        $value = (func_num_args() === 0) ? self::$puts : cls_arr::get(self::$puts, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
	}

	/**
	 * Fetch an item from the php://input for patch arguments
	 *
	 * @param   string  $index    The index key
	 * @param   mixed   $default  The default value
	 * @return  string|array
	 */
	public static function patch( $index = null, $default = null, $filter_type = '' )
	{
        $value = (func_num_args() === 0) ? self::$patchs : cls_arr::get(self::$patchs, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
	}

	/**
	 * Fetch an item from the php://input for delete arguments
	 *
	 * @param   string  $index    The index key
	 * @param   mixed   $default  The default value
	 * @return  string|array
	 */
	public static function delete( $index = null, $default = null, $filter_type = '' )
	{
        $value = (func_num_args() === 0) ? self::$deletes : cls_arr::get(self::$deletes, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
	}

    /**
     * Get the request body interpreted as JSON.
     *
     * @param   mixed  $index
     * @param   mixed  $default
     * @return  array  parsed request body content.
     */
    public static function json( $index = null, $default = null, $filter_type = '' )
    {
        $value = (func_num_args() === 0) ? self::$jsons : cls_arr::get(self::$jsons, $index, $default);
        return cls_filter::filter($value, $filter_type, self::$throw_error);
    }

    /**
     * Get the request body interpreted as XML.
     *
     * @param   mixed  $index
     * @param   mixed  $default
     * @return  array  parsed request body content.
     */
    public static function xml( $index = null, $default = null, $filter_type = '' )
    {
        $value = (func_num_args() === 0) ? self::$xmls : cls_arr::get(self::$xmls, $index, $default);
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
	 * @return  array
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

				$value = static::server('Content_Type', static::server('Content-Type')) and $headers['Content-Type'] = $value;
				$value = static::server('Content_Length', static::server('Content-Length')) and $headers['Content-Length'] = $value;
			}
			else
			{
				$headers = getallheaders();
			}
		}

		return empty($headers) ? $default : ((func_num_args() === 0) ? $headers : cls_arr::get(array_change_key_case($headers), strtolower($index), $default));
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
     * 获得国家代码
     * 
     * @param string $ip
     * @return void
     */
    public static function country($ip = '')
    {
        // 如果是通过IP来获取城市地址的
        if (!empty($ip)) 
        {
            if (!file_exists(APPPATH.'/../../IP-COUNTRY-ISP.BIN')) 
            {
                return "HK";
            }
            $db = new cls_ip2location(APPPATH.'/../../IP-COUNTRY-ISP.BIN', cls_ip2location::FILE_IO);
            $records = $db->lookup($ip, array(cls_ip2location::COUNTRY_CODE));
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
            $records = $db->lookup($ip, array(cls_ip2location::COUNTRY_CODE));
            return strtoupper($records['countryCode']);
        }
    }

    public static function language()
    {
        if ($lang = self::cookie("language"))
        {
            return $lang;
        }

        $languages = array();
        if ( !empty(self::server('HTTP_ACCEPT_LANGUAGE')) )
        {
            $languages = explode(',', preg_replace('/(;\s?q=[0-9\.]+)|\s/i', '', strtolower(trim(self::server('HTTP_ACCEPT_LANGUAGE')))));
        }

        if (count($languages) === 0)
        {
            $languages = array('Undefined');
        }

        $lang = !in_array($languages[0], array("zh-cn", "zh-tw", "en", "km")) ? "zh-cn" : $languages[0];
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
     * 获得当前网址，不包含 Query String
     */
    public static function url($uri = '', $protocol = null)
    {
        $protocol = $protocol ? $protocol : self::server('REQUEST_SCHEME');
        return $protocol.'://'.self::server('SERVER_NAME').$uri;    
    }

    /**
     * 获得当前完整网址，包含 Query String
     * 
     * @return string
     */
    public static function full_url()
    {
        return self::url().'?'.self::query_string();    
    }

    public static function path()
    {
        return self::cururl();
    }

    /**
     * 获得当前请求路径
     * new name path
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
     * 客户端请求的编码是否 JSON 内容 
     * Accept 属于请求头，表示客户端希望接收到的数据内容
     * Content-Type 属于实体头，表示客户端发送到服务端的数据内容
     * 
     * @return bool
     */
    public static function is_json()
    {
        if (self::item('is_json', 0, 'int'))
        {
            return true;
        }

        $http_accept = req::server('HTTP_ACCEPT');
        $http_accepts = explode(';', $http_accept);
        $http_accept = $http_accepts[0];
        $http_accepts = explode(',', $http_accept);
        return in_array('application/json', $http_accepts);
    }

    /**
     * jquery 发出 ajax 请求时，会在请求头部添加一个名为X-Requested-With的信息，信息内容为 XMLHttpRequest
     * js 需要如下处理
     * var xmlhttp=new XMLHttpRequest(); 
     * xmlhttp.open("GET","test.php",true); 
     * xmlhttp.setRequestHeader("X-Requested-With","XMLHttpRequest"); 
     * xmlhttp.send();
     * 
     * @return bool
     */
    public static function is_ajax()
    {
        // 如果强制设置为true，一般用于接口
        if (self::$is_force_ajax === true) 
        {
            return true;
        }

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
     * @param string $gourl
     *
     * @return string
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
     * 跳转页
     * 
     * @param string $gourl
     *
     * @return string
     */
    public static function redirect($gourl = '')
    {
        $gourl = self::cookie('gourl', $gourl);
        $gourl = urldecode($gourl);
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
                        return copy(self::$files[$formname]['tmp_name'], $filename);
                    else
                        return move_uploaded_file(self::$files[$formname]['tmp_name'], $filename);
                }
                else 
                {
                    if( PHP_OS == 'WINNT')
                        return copy(self::$files[$formname]['tmp_name'][$item], $filename);
                    else 
                        return move_uploaded_file(self::$files[$formname]['tmp_name'][$item], $filename);
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
     * @param  string  $subfix
     *
     * @return bool
     */
    public static function check_subfix($formname, $subfix = array('csv'), $item= '')
    {
        if( !in_array(self::get_shortname( $formname, $item ), $subfix) )
        {
            return false;
        }
        return true;
    }

    /**
     * 把指定数据转化为路由数据
     *
     * @param  $dfarr   默认数据列表 array( array(key, dfvalue)... )
     * @param  $datas   数据列表
     * @param  $method  方法
     *
     * @return bool
     */
    public static function assign_values(&$dfarr, &$datas = null, $method = 'GET')
    {
        $method = strtoupper( $method );
        foreach($dfarr as $k => $v)
        {
            if( isset($datas[$k]) )
            {
                req::$forms[ $v[0] ] = $datas[$k];
            }
            else 
            {
                req::$forms[ $v[0] ] = $v[1];
            }
            //给值gets/posts
            if( $method == 'GET' ) 
            {
                req::$gets[ $v[0] ] = req::$forms[ $v[0] ];
            }
            else 
            {
                req::$posts[ $v[0] ] = req::$forms[ $v[0] ];
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
        $content_header = self::headers('Content-Type');
        if (($content_type = strstr($content_header, ';', true)) === false)
        {
            $content_type = $content_header;
        }

        // fetch the raw input data
        $php_input = self::raw();

        // handle form-urlencoded input
        if ( $content_type == 'application/x-www-form-urlencoded' )
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
            $php_input = array();
            foreach ($blocks as $id => $block)
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

            self::$jsons = $php_input;

            self::${$method.'s'} = $php_input;
        }

        // handle xml input
        elseif ($content_type == 'application/xml' or $content_type == 'text/xml')
        {
            self::$xmls = $php_input = self::xml_to_array(new \SimpleXMLElement($php_input));
        }

        // unknown input format
        elseif ($php_input and ! is_array($php_input))
        {
            // don't know how to handle it, allow the application to handle it
            // reset the method to avoid having it stored below!
            $method = null;
        }

        $magic_quotes_gpc = ini_get('magic_quotes_gpc');

        //命令行模式
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

        // 处理get
        if( count($_GET) > 0 )
        {
            if( !$magic_quotes_gpc ) $_GET = self::add_s( $_GET );
            if (self::$config['global_xss_filtering']) $_GET = cls_security::xss_clean($_GET);
            self::$gets = $_GET;
        }

        // 处理post
        if( count($_POST) > 0 )
        {
            if( !$magic_quotes_gpc ) $_POST = self::add_s( $_POST );
            if (self::$config['global_xss_filtering']) $_POST = cls_security::xss_clean($_POST);
            self::$posts = $_POST;
        }

        //处理cookie
        if( count($_COOKIE) > 0 )
        {
            if( !$magic_quotes_gpc ) $_COOKIE = self::add_s( $_COOKIE );
            if (self::$config['global_xss_filtering']) $_COOKIE = cls_security::xss_clean($_COOKIE);
            self::$cookies = $_COOKIE;
        }

        // 处理request
        if( count($_REQUEST) > 0 )
        {
            if( !$magic_quotes_gpc ) $_REQUEST = self::add_s( $_REQUEST );
            if (self::$config['global_xss_filtering']) $_REQUEST = cls_security::xss_clean($_REQUEST);
            self::$forms = $_REQUEST;
        }

        //上传的文件处理
        if( isset($_FILES) && count($_FILES) > 0 )
        {
            if( !$magic_quotes_gpc ) $_FILES = self::add_s( $_FILES );
            self::filter_files($_FILES);
        }

        unset($_GET);
        unset($_POST);
        unset($_REQUEST);

        // store the parsed data based on the request method
        if ($method == 'put' or $method == 'patch' or $method == 'delete')
        {
            self::${$method.'s'} = $php_input;
        }

        //// 是否启用rewrite(保留参数)
        //self::$url_rewrite = isset($GLOBALS['config']['use_rewrite']) ? $GLOBALS['config']['use_rewrite'] : false;
        //self::$url_rewrite = false;

        ////处理url_rewrite(暂时不实现)
        //if( self::$url_rewrite )
        //{
            //$gstr = self::server('QUERY_STRING');

            //if( empty($gstr) )
            //{
                //$gstr = self::server('PATH_INFO');
            //}
        //}
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

