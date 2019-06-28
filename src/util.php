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
use kaliphp\lib\cls_redis_lock;
use kaliphp\lib\cls_snowflake;

/**
 * 实用函数集合
 *
 * 替代lib_common
 *
 * @author seatle<seatle@foxmail.com>
 * @version $Id$  
 */
class util
{
    public static $client_ip = NULL;

    public static $cfc_handle = NULL;

    public static $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; zh-CN; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13';

    /**
     * 文件锁
     * 上锁失败，提示并且返回
     * if ( !util::lock('crond_test'))
     * {
     *     echo "process has been locked\n";
     *     return;
     * }
     *
     * // 上锁成功，干活
     * ...
     * // 解锁
     * util::unlock('crond_test');
     * 
     * @param mixed $lock_name
     * @param int $lock_timeout
     * @return bool
     */
    public static function lock($lock_name, $lock_timeout = 600)
    {
        $lock = self::get_file(kali::$data_root."/lock/{$lock_name}.lock");
        if ( $lock ) 
        {
            $lock_time = time() - $lock;
            // 还没到10分钟，说明进程还活着
            if ($lock_time < $lock_timeout) 
            {
                // 上锁失败
                return false;
            }
        }

        self::put_file(kali::$data_root."/lock/{$lock_name}.lock", time());
        return true;
    }

    public static function unlock($lock_name)
    {
        return unlink(kali::$data_root."/lock/{$lock_name}.lock");
    }

    /**
     * 获取文件后缀名
     */
    public static function file_ext($filename)
    {
        $arr = explode(".", $filename);
        return end($arr);
    }

    /**
     * 检查路径是否存在
     * @param $path
     * @return string | bool
     */
    public static function path_exists( $path )
    {
        $pathinfo = pathinfo ( $path . '/tmp.txt' );
        if ( !empty( $pathinfo ['dirname'] ) )
        {
            if (file_exists ( $pathinfo ['dirname'] ) === false)
            {
                if (@mkdir ( $pathinfo ['dirname'], 0777, true ) === false)
                {
                    if(file_exists ( $pathinfo ['dirname'] ))
                    {
                        return $path;
                    }
                    return false;
                }
            }
        }
        return $path;
    }

    /**
     * 读文件
     */
    public static function get_file($url, $timeout = 10)
    {
        if (function_exists('curl_init'))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $content = curl_exec($ch);
            curl_close($ch);
            if ($content) return $content;
        }
        $ctx = stream_context_create(array('http' => array('timeout' => $timeout)));
        $content = @file_get_contents($url, 0, $ctx);
        if ($content) return $content;
        return false;
    }

    /**
     * 写文件
     */
    public static function put_file($file, $content, $flag = 0)
    {
        $pathinfo = pathinfo ( $file );
        if (! empty ( $pathinfo ['dirname'] ))
        {
            if (file_exists ( $pathinfo ['dirname'] ) === false)
            {
                if (@mkdir ( $pathinfo ['dirname'], 0777, true ) === false)
                {
                    return false;
                }
            }
        }
        if ($flag === FILE_APPEND)
        {
            return @file_put_contents ( $file, $content, FILE_APPEND );
        }
        else
        {
            return @file_put_contents ( $file, $content, LOCK_EX );
        }
    }

    /**
     * 判断是否为utf8字符串
     * @param $str
     * @return bool
     */
    public static function is_utf8($str)
    {
        if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * utf8编码模式的中文截取2，单字节截取模式
     * 这里不使用mbstring扩展
     * @return string
     */
    public static function utf8_substr($str, $slen, $startdd=0)
    {
        return mb_substr($str , $startdd , $slen , 'UTF-8');
    }

    /**
     * utf-8中文截取，按字数截取模式
     * @return string
     */
    public static function utf8_substr_num($str, $length, $start=0)
    {
        preg_match_all('/./su', $str, $ar);
        if( count($ar[0]) <= $length ) 
        {
            return $str;
        }
        $tstr = '';
        $n = 0;
        for($i=0; isset($ar[0][$i]); $i++)
        {
            if($n < $length)
            {
                $tstr .= $ar[0][$i];
                $n++;
            } 
            else 
            {
                break;
            }
        }
        return $tstr;
    }

    /** 
     * 把秒数转换为时分秒的格式 
     * @param Int $times 时间，单位 秒 
     * @return String 
     */  
    public static function second2time($seconds)
    {  
        //$seconds = 3500;
        $seconds = (int)$seconds;
        if ( $seconds < 0 ) 
        {
            return 0;
        }
        // 大于一个小时
        if( $seconds>3600 )
        {
            $days_num = '';
            // 大于一天
            if( $seconds>24*3600 )
            {
                $days		= (int)($seconds/86400);
                $days_num	= $days."天";
                $seconds	= $seconds%86400;//取余
            }
            $hours = intval($seconds/3600);
            $minutes = $seconds%3600;//取余下秒数
            $time = $days_num.$hours."小时".gmstrftime('%M分钟%S秒', $minutes);
        }
        // 等于一个小时
        elseif( $seconds == 3600 )
        {
            $time = gmstrftime('1小时', $seconds);
        }
        // 小于一小时
        else
        {
            // 大于一分钟
            if( $seconds>60 )
            {
                $time = gmstrftime('%M分钟%S秒', $seconds);
            }
            // 等于一分钟
            elseif( $seconds == 60 )
            {
                $time = gmstrftime('1分钟', $seconds);
            }
            // 小于一分钟
            else 
            {
                $time = gmstrftime('%S秒', $seconds);
            }
        }
        return $time;
    }

    /**
     * 从普通时间返回Linux时间截(strtotime中文处理版)
     * @param string $dtime
     * @return int
     */
    public static function cn_strtotime( $dtime )
    {
        if(!preg_match("/[^0-9]/", $dtime))
        {
            return $dtime;
        }
        $dtime = trim($dtime);
        $dt = Array(1970, 1, 1, 0, 0, 0);
        $dtime = preg_replace("/[\r\n\t]|日|秒/", " ", $dtime);
        $dtime = str_replace("年", "-", $dtime);
        $dtime = str_replace("月", "-", $dtime);
        $dtime = str_replace("时", ":", $dtime);
        $dtime = str_replace("分", ":", $dtime);
        $dtime = trim(preg_replace("/[ ]{1,}/", " ", $dtime));
        $ds = explode(" ", $dtime);
        $ymd = explode("-", $ds[0]);
        if(!isset($ymd[1]))
        {
            $ymd = explode(".", $ds[0]);
        }
        if(isset($ymd[0]))
        {
            $dt[0] = $ymd[0];
        }
        if(isset($ymd[1])) $dt[1] = $ymd[1];
        if(isset($ymd[2])) $dt[2] = $ymd[2];
        if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
        if(isset($ds[1]))
        {
            $hms = explode(":", $ds[1]);
            if(isset($hms[0])) $dt[3] = $hms[0];
            if(isset($hms[1])) $dt[4] = $hms[1];
            if(isset($hms[2])) $dt[5] = $hms[2];
        }
        foreach($dt as $k=>$v)
        {
            $v = preg_replace("/^0{1,}/", '', trim($v));
            if($v=='')
            {
                $dt[$k] = 0;
            }
        }
        $mt = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);
        if(!empty($mt))
        {
            return $mt;
        }
        else
        {
            return strtotime( $dtime );
        }
    }

    /**
     * Check if a string is json encoded
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_json($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Check if a string is a valid XML
     *
     * @param  string  $string  string to check
     * @return bool
     * @throws \Exception
     */
    public static function is_xml($string)
    {
        if ( ! defined('LIBXML_COMPACT'))
        {
            throw new \Exception('libxml is required to use Str::is_xml()');
        }

        $internal_errors = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($string) !== false;
        libxml_use_internal_errors($internal_errors);

        return $result;
    }

    /**
     * Check if a string is serialized
     *
     * @param  string  $string  string to check
     * @return bool
     */
    public static function is_serialized($string)
    {
        $array = @unserialize($string);
        return ! ($array === false and $string !== 'b:0;');
    }

    /**
     * Check if a string is html
     *
     * @param  string $string string to check
     * @return bool
     */
    public static function is_html($string)
    {
        return strlen(strip_tags($string)) < strlen($string);
    }

    // 转换大小单位
    public static function convert($size)
    {
        if ( empty($size)) 
        {
            return '0';
        }
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    // 解决自带函数scandir被禁用的尴尬
    public static function scandir($dir)
    {
        // 定义用于存储文件名的数组
        $array_file = array();
        $handle = @opendir($dir);
        if ( !$handle) 
        {
            return false;
        }

        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..") 
            {
                $array_file[] = $file;
            }
        }
        closedir($handle);
        return $array_file;
    }

    // 字符串转数字，用于分表和图片分目录
    public static function str2number($str, $maxnum = 128)
    {
        // 位数
        $bitnum = 1;
        if ($maxnum >= 100) 
        {
            $bitnum = 3;
        }
        elseif ($maxnum >= 10) 
        {
            $bitnum = 2;
        }

        // sha1:返回一个40字符长度的16进制数字
        $str = sha1(strtolower($str));
        // base_convert:进制建转换，下面是把16进制转成10进制，方便做除法运算
        // str_pad:把字符串填充为指定的长度，下面是在左边加0，共 $bitnum 位
        $str = str_pad(base_convert(substr($str, -2), 16, 10) % $maxnum, $bitnum, "0", STR_PAD_LEFT);
        return $str;
    }

    // 生成订单ID，19位，刚好是mysql的bigint类型
    // 16位
    public static function order_id($num = 7)
    {
        //return cls_snowflake::instance(0, 1)->nextid();
        return date("ymdHis").self::random('numeric', $num);
    }

    /**
     * 判断数组是否有序
     *
     * @param array $arr
     * @return bool
     */
    public static function is_ordinal_array( array $arr = null )
    {
        if ( $arr === null )
        {
            return false;
        }

        $i = 0;
        foreach ($arr as $k => $v)
        {
            if ($i++ !== $k)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 时区转换
     * 
     * @param mixed $datetime           KALI_TIMESTAMP, 可以是时间戳或者时间格式
     * @param string $format            '', 格式化输出字符串。默认为Y-m-d H:i:s
     * @param string $from_timezone     'ETC/GMT-7', 默认为系统设置的时区，即 ETC/GMT
     * @param string $to_timezone       'ETC/GMT-8', 转换成为的时区，默认获取用户所在国家对应时区
     * @return string
     */
    public static function to_timezone($datetime = KALI_TIMESTAMP, $format = 'Y-m-d H:i:s', $from_timezone = null, $to_timezone = null) 
    {
        // 如果没有传时区，用国家代码从配置文件获取对应时区
        $to_timezone   = empty($to_timezone)   ? config::instance('config')->get(COUNTRY, 'timezone')  : $to_timezone;
        // 配置文件也没有找到时区，使用默认配置的时区
        $to_timezone   = empty($to_timezone)   ? config::instance('config')->get('to_timezone')  : $to_timezone;
        $from_timezone = empty($from_timezone) ? config::instance('config')->get('timezone_set') : $from_timezone;
        // 支持时间戳和时间格式
        $datetime      = is_numeric($datetime) ? '@'.$datetime : $datetime;

        $date_obj = new \DateTime($datetime, new \DateTimeZone($from_timezone));
        $date_obj->setTimezone(new \DateTimeZone($to_timezone));
        return $date_obj->format($format);
    }

    /**
     * Creates a random string of characters
     *
     * @param   string  $type    the type of string
     * @param   int     $length  the number of characters
     * @return  string  the random string
     */
    public static function random($type = 'alnum', $length = 16)
    {
        switch($type)
        {
            case 'basic':
                return mt_rand();
                break;

            default:
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
            case 'distinct':
            case 'hexdec':
                switch ($type)
                {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    default:
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;

                    case 'numeric':
                        $pool = '0123456789';
                        break;

                    case 'nozero':
                        $pool = '123456789';
                        break;

                    case 'distinct':
                        $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
                        break;

                    case 'hexdec':
                        $pool = '0123456789abcdef';
                        break;
                }

                $str = '';
                for ($i=0; $i < $length; $i++)
                {
                    $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
                }
                return $str;
                break;

            case 'unique':
                return md5(uniqid(mt_rand()));
                break;

            case 'sha1' :
                return sha1(uniqid(mt_rand(), true));
                break;

            case 'uuid':
                $pool = array('8', '9', 'a', 'b');
                return sprintf('%s-%s-4%s-%s%s-%s',
                    static::random('hexdec', 8),
                    static::random('hexdec', 4),
                    static::random('hexdec', 3),
                    $pool[array_rand($pool)],
                    static::random('hexdec', 3),
                    static::random('hexdec', 12));
                break;

            case 'web':
                // 即使同一个IP，同一款浏览器，要在微妙内生成一样的随机数，也是不可能的
                // 进程ID保证了并发，微妙保证了一个进程每次生成都会不同，IP跟AGENT保证了一个网段
                return md5(getmypid().microtime().$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                break;
        }
    }

    /**
     * Code Highlighter
     *
     * Colorizes code strings
     *
     * @param	string	the text string
     * @return	string
     */
    public static function highlight_code($str)
    {
        $str = str_replace(
            array('&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>'),
            array('<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
            $str
        );

        $str = highlight_string('<?php '.$str.' ?>', TRUE);

        $str = preg_replace(
            array(
                '/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
                '/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
                '/<span style="color: #[A-Z0-9]+"\><\/span>/i'
            ),
            array(
                '<span style="color: #$1">',
                "$1</span>\n</span>\n</code>",
                ''
            ),
            $str
        );

        return str_replace(
            array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
            array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'),
            $str
        );
    }

    public static function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
            $non_displayables[] = '/%7f/i';	// url encoded 127
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ($count);

        return $str;
    }

    public static function get_soap_client($url) 
    {
        libxml_disable_entity_loader(false);
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );
        $context = stream_context_create($opts);

        $soap_client_options = array(
            'stream_context' => $context,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'encoding' => 'UTF-8',
            'verifypeer' => false,
            'verifyhost' => false,
            'soap_version' => SOAP_1_1,
            'exceptions' => 1,
        );
        //$html = file_get_contents($url);
        //error_log(date('Y-m-d H:i:s') . '||'. ($html) . "\n", 3, PATH_DATA . '/ectrip_xml.log');

        $client = new \SoapClient($url, $soap_client_options);
        return $client;
    }

    /*
     * http get函数
     * @param $url
     * @param $$timeout=30
     * @param $referer_url=''
     */
    public static function http_get($url, $timeout=30, $referer_url='')
    {
        $startt = time();
        if (function_exists('curl_init'))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            if( $referer_url != '' )  curl_setopt($ch, CURLOPT_REFERER, $referer_url);
            curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent);
            $result = curl_exec($ch);
            $errno  = curl_errno($ch);
            curl_close($ch);
            return $result;
        }
        else
        {
            $Referer = ($referer_url=='' ?  '' : "Referer:{$referer_url}\r\n");
            $context =
                array('http' =>
                array('method' => 'GET',
                    'header' => 'User-Agent:'.self::$user_agent."\r\n".$Referer
                )
            );
            $contextid = stream_context_create($context);
            $sock = fopen($url, 'r', false, $contextid);
            stream_set_timeout($sock, $timeout);
            if($sock)
            {
                $result = '';
                while (!feof($sock)) {
                    //$result .= stream_get_line($sock, 10240, "\n");
                    $result .= fgets($sock, 4096);
                    if( time() - $startt > $timeout ) {
                        return '';
                    }
                }
                fclose($sock);
            }
        }
        return $result;
    }

    /**
     * 向指定网址发送post请求
     * @param $url
     * @param $query_str
     * @param $$timeout=30
     * @param $referer_url=''
     * @return string
     */
    public static function http_post($url, $query_str, $timeout=30, $referer_url='')
    {
        $startt = time();
        if( function_exists('curl_init') )
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_str);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent );
            $result = curl_exec($ch);
            $errno  = curl_errno($ch);
            curl_close($ch);
            //echo " $url & $query_str <hr /> $errno , $result ";
            return $result;
        }
        else
        {
            $context =
                array('http' =>
                array('method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                    'User-Agent: '.self::$user_agent."\r\n".
                    'Content-length: ' . strlen($query_str),
                    'content' => $query_str));
            $contextid = stream_context_create($context);
            $sock = fopen($url, 'r', false, $contextid);
            if ($sock)
            {
                $result = '';
                while (!feof($sock))
                {
                    $result .= fgets($sock, 4096);
                    if( time() - $startt > $timeout ) {
                        return '';
                    }
                }
                fclose($sock);
            }
        }
        return $result;
    }

    /**
     * 向指定网址post文件
     * @param $url
     * @param $files  文件数组 array('fieldname' => filepathname ...)
     * @param $fields 附加的数组  array('fieldname' => content ...)
     * @param $$timeout=30
     * @param $referer_url=''
     * @return string
     */
    public static function http_post_file($url, $files, $fields, $timeout=30, $referer_url='')
    {
        $startt = time();
        if( function_exists('curl_init') )
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, self::$user_agent );
            $need_class = class_exists('\CURLFile') ? true : false;
            foreach($files as $k => $v)
            {
                if ( $need_class ) {
                    $fields[$k] = new \CURLFile(realpath($v));
                } else {
                    $fields[$k] = '@' . realpath($v);
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
        else
        {
            return false;
        }
    }

    public static function return_json($array)
    {
        header('Content-type: application/json');
        exit(json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }

    public static function json_encode( $array )
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    // 空数组转对象
    public static function empty_array2object( $str )
    {
        // Is the string an array?
        if ( !is_array($str) )
        {
            return $str;
        }

        // 遇到空数组对象[{}]就挂逼了
        if ( empty($str) ) 
        {
            return new \stdClass(); 
        }

        foreach ($str as $k => &$v)
        {
            $str[$k] = self::empty_array2object($v);
        }
        return $str;
    }

    /**
     * 下载文件
     * 
     * @param mixed $doc_name   源文件名
     * @param mixed $out_name   输出的文件名
     */
    public static function download_file($doc_name, $out_name)
    {
        $sourceFile = $doc_name; //要下载的临时文件名
        $outFile = $out_name; //下载保存到客户端的文件名
        if (!is_file($sourceFile)) {
            die("<b>404 File not found!</b>");
        }
        $len = filesize($sourceFile); //获取文件大小
        $filename = basename($sourceFile); //获取文件名字
        $outFile_extension = strtolower(substr(strrchr($sourceFile, "."), 1)); //获取文件扩展名
        //var_dump($outFile_extension);exit();exit();

        //根据扩展名 指出输出浏览器格式
        switch ($outFile_extension) {
        case "PDF" :
            $ctype = "application/PDF";
            break;
        case "zip" :
            $ctype = "application/zip";
            break;
        case "doc" :
            $ctype = "application/doc";
            break;
        case "mp3" :
            $ctype = "audio/mpeg";
            break;
        case "mpg" :
            $ctype = "video/mpeg";
            break;
        case "avi" :
            $ctype = "video/x-msvideo";
            break;
        case "rar" :
            $ctype = "application/rar";
            break;
        case "wps" :
            $ctype = "application/wps";
            break;
        default :
            $ctype = "application/force-download";
        }
        //Begin writing headers

        header("Cache-Control:");
        header("Cache-Control: public");

        //设置输出浏览器格式
        header("Content-Type: $ctype");

        header("Content-Disposition: attachment; filename=" . $outFile);
        header("Accept-Ranges: bytes");
        $size = filesize($sourceFile);
        //如果有$_SERVER['HTTP_RANGE']参数
        if (isset ($_SERVER['HTTP_RANGE'])) {
            /*Range头域 　　Range头域可以请求实体的一个或者多个子范围。
            例如，
            表示头500个字节：bytes=0-499
            表示第二个500字节：bytes=500-999
            表示最后500个字节：bytes=-500
            表示500字节以后的范围：bytes=500- 　　
            第一个和最后一个字节：bytes=0-0,-1 　　
            同时指定几个范围：bytes=500-600,601-999 　　
            但是服务器可以忽略此请求头，如果无条件GET包含Range请求头，响应会以状态码206（PartialContent）返回而不是以200 （OK）。
             */
            // 断点后再次连接 $_SERVER['HTTP_RANGE'] 的值 bytes=4390912-
            list ($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            //if yes, download missing part
            str_replace($range, "-", $range); //这句干什么的呢。。。。
            $size2 = $size - 1; //文件总字节数
            $new_length = $size2 - $range; //获取下次下载的长度
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: $new_length"); //输入总长
            header("Content-Range: bytes $range$size2/$size"); //Content-Range: bytes 4908618-4988927/4988928   95%的时候
        } else {
            //第一次连接
            $size2 = $size - 1;
            header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
            header("Content-Length: " . $size); //输出总长
        }
        //打开文件
        $fp = fopen("$sourceFile", "rb");
        file_put_contents("/tmp/download.log","step1\n",FILE_APPEND);
        //设置指针位置
        fseek($fp, $range);
        //虚幻输出
        while (!feof($fp)) {
            file_put_contents("/tmp/download.log","step2\n",FILE_APPEND);
            //设置文件最长执行时间
            set_time_limit(0);
            print (fread($fp, 1024 * 8)); //输出文件
            flush(); //输出缓冲
            ob_flush();
        }
        file_put_contents("/tmp/download.log","step3\n",FILE_APPEND);
        fclose($fp);
        exit ();
    }

    /**
     * 将cgi进程提前结束,让余下部分在结束后继续操作,减少用户等待时间
     *   util::shutdown_function(
     *       array('class', 'function'), //调用类传数组，调用非类方法，直接传字符串'xxx'
     *       array($param1, $param2....)//参数是数组形式array
     *   );
     * example:
     *   util::shutdown_function(['kaliphp\log', 'error'], ['xxxxx']);
     **/
    public static function shutdown_function($func, $params = array(), $end = false)
    {
        static $stack = array();

        if( $func )
        {
            $stack[] = array(
                'func' => $func,
                'params' => $params
            );
        }

        if($end)
        {
            function_exists('fastcgi_finish_request') && fastcgi_finish_request();
            foreach($stack as $v)
            {
                call_user_func_array($v['func'], $v['params']);
            }
        }
    }

    /**
     * @author han 
     * 尝试从数组/对象中获取值
     * @param mixed $src 源
     * @param string $key 键 array支持 keypath
     * @param mixed $default 默认值
     * @param int mode 模式。0：使用 empty；1：使用 isset
     * @param callable $filter 对值进行过滤的函数
     * @param bool $process_scalar 是否处理标量
     * @return mixed
     */
    public static function get_value($src, $key, $default = NULL, $mode = 0, $filter = NULL, $process_scalar = FALSE)
    {
        $value = NULL;
        if ($process_scalar)
        {
            if (is_scalar($src))
            {
                $value = $src;
            }
        }
        
        if (is_array($src))
        {
            $value = $src;
            $key_path = explode('/', $key);
            foreach ($key_path as $k)
            {
                if (isset($value[$k]))
                {
                    $value = $value[$k];
                }
                else
                {
                    $value = $default;
                    break;
                }
            }
        }
        
        if (is_object($src))
        {
            $value = property_exists($src, $key) ? $src->$key : $default;
        }
        
        if ($mode === 0)
        {
            if (empty($value))
            {
                $value = $default;
            }
        }
        else
        {
            if (!isset($value))
            {
                $value = $default;
            }
        }
        
        if ($filter && is_callable($filter) && is_scalar($value))
        {
            $value = call_user_func($filter, $value);
        }
        
        return $value;
    }

    /**
      * @author han
      * 加原子锁并进程结束后自动解锁
      * @param  [type]  $name     锁的标识名
      * @param  integer $timeout  循环获取锁的等待超时时间，在此时间内会一直尝试获取锁直到超时，为0表示失败后直接返回不等待
      * @param  integer $expire   当前锁的最大生存时间(秒)，必须大于0，如果超过生存时间锁仍未被释放，则系统会自动强制释放
      * @return bool              true表示上锁成功 false表示上锁失败   
     */
    public static function auto_lock($lock_name, $timeout = 0, $expire = 15)
    {
        if( cls_redis_lock::lock($lock_name, $timeout, $expire) )
        {
            util::shutdown_function(
                ['kaliphp\lib\cls_redis_lock', 'unlock'],
                [$lock_name, true]
            );

            return true;
        }

        return false;
    }

    /**
     * curl 函数
     * @Author han
     * @param  [type]  $data  请求参数
     * data支持下面参数（只有url是必须的，其他都是可选的）
     * url     url地址
     * post    有的话就是post,没有就是get post的数据，可以是数组或者http_build_query后的值
     * timeout 超时时间
     * ip      伪造ip
     * referer 来源
     * cookie  传递cookie
     * cookie_file cookie路径
     * save_cookie cookie保存路径
     * proxy   代理信息
     * header  http请求头 
     * debug   是否开启调试
     * $tmp = util::http_request(['url' => 'http://www.taobao.com']);
     * $tmp['body']就是返回的内容
     * @param  boolean $multi 是否并发模式
    * $tmp = util::http_request([
    *     ['url' => 'http://www.taobao.com'],
    *     ['url' => 'http://www.baidu.com', 'post' => ['a' => 1, 'b' => 2] ],
    * ], true);
    * $tmp['body']就是返回的内容
     * @return array   curl执行结果
     */
    static public function http_request($data, $multi = false)
    {
        if(!isset($data['url']) && ($tmp = current($data)) && isset($tmp['url']))
        {
            static $curl_multi;
            
            $curl_multi === null && 
            $curl_multi = function_exists('curl_multi_init') && 
            strpos(ini_get('disable_functions'), 'curl_multi_init') === false;
            
            if($curl_multi && $multi)
            {
                //curl并发模式
                $mch = curl_multi_init();
                
                $ch = $ret = $error = array();
                foreach($data as $k => $v)
                {
                    $v['return_curl'] = true;
                    $ch[$k] = self::http_request($v);
                    $ret[$k] = array('head' => '', 'body' => null);
                    
                    curl_multi_add_handle($mch, $ch[$k]);
                }
                
                $active = null;
                //execute the handles
                do 
                {
                    $mrc = curl_multi_exec($mch, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                

                while ($active && $mrc == CURLM_OK) 
                {
                    while (curl_multi_exec($mch, $active) === CURLM_CALL_MULTI_PERFORM);
                    if (curl_multi_select($mch) != -1) 
                    {
                        do {
                            $mrc = curl_multi_exec($mch, $active);
                            $info = curl_multi_info_read($mch);
                            if($info !== false && $info['result'])
                            {
                                foreach($ch as $k => $v)
                                {
                                    if($v === $info['handle'])
                                    {
                                        $tmp = curl_getinfo($info['handle']);
                                        $error[$k] = array($info['result'], curl_error($info['handle']), $tmp['url']);
                                        break;
                                    }
                                }
                            }
                        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                    }
                }
                
                /*do{
                    $mrc = curl_multi_exec($mch, $active);
                    curl_multi_select($mch);
                    $info = curl_multi_info_read($mch);
                    if($info !== false && $info['result']){
                        foreach($ch as $k => $v){
                            if($v === $info['handle']){
                                $tmp = curl_getinfo($info['handle']);
                                $error[$k] = array($info['result'], curl_error($info['handle']), $tmp['url']);
                                break;
                            }
                        }
                    }
                }while($active > 0);*/

                $error_log = '';
                foreach($ch as $k => $v)
                {
                    if(isset($error[$k]))
                    {
                        $ret[$k]['body'] = null;
                        $ret[$k]['info']['status'] = 0;
                        $ret[$k]['info']['errno'] = $error[$k][0];
                        $error_log .= "{$error[$k][2]}|{$error[$k][0]}|{$error[$k][1]}\n";
                        
                        continue;
                    }
                    
                    $ret[$k]['body'] = curl_multi_getcontent($v);
                    
                    $info = curl_getinfo($v);
                    $ret[$k]['info']['status'] = $info['http_code'];
                    curl_multi_remove_handle($mch, $ch[$k]);
                }
                
                if(!empty($error))
                {
                    log::error($error_log);
                }
                
                curl_multi_close($mch);
                
                return $ret;
                
            }
            else
            {
                $ret = array();
                foreach($data as $k => $v)
                {
                    $ret[$k] = self::http_request($v);
                }
                return $ret;
            }
        }
        
        $data['post'] = isset($data['post']) ? (is_array($data['post']) ? http_build_query($data['post']) : $data['post']) : '';
        $data['cookie'] = isset($data['cookie']) ? $data['cookie'] : '';
        $data['ip'] = isset($data['ip']) ? $data['ip'] : '';
        $data['timeout'] = isset($data['timeout']) ? $data['timeout'] : 15;
        $data['block'] = isset($data['block']) ? $data['block'] : true;
        $data['referer'] = isset($data['referer']) ? $data['referer'] : '';
        $data['connection'] = isset($data['connection']) ? $data['connection'] : 'close';
        $data['header'] = isset($data['header']) ? (array)$data['header'] : array();
        
        if(function_exists('curl_init'))
        {
            $ch = curl_init($data['url']);
            
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, !empty($data['UA']) ? $data['UA'] : 'Mozilla/5.0');
            if( !empty($data['ip']) ) 
            {
                $x_forwarded_for = $data['ip'];
                $client = empty($data['client']) ? $x_forwarded_for : $data['client'];
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$x_forwarded_for}", "CLIENT-IP:{$client}"));
            }

            if(!empty($data['debug']))
            {
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $fp = fopen($data['debug'], 'a');
                curl_setopt($ch, CURLOPT_STDERR, $fp);
                //fclose($fp);
            }
            //curl_setopt($ch, CURLOPT_ENCODING, 'none');
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($data['header'], array(
                'Connection: '. $data['connection']
            )));
            
            if(stripos($data['url'], 'https://') === 0)
            {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
                //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            
            if(!empty($data['referer'])) curl_setopt($ch, CURLOPT_REFERER, $data['referer']);
            if(!empty($data['cookie'])) curl_setopt($ch, CURLOPT_COOKIE, $data['cookie']);
            if(!empty($data['cookie_file'])) curl_setopt($ch, CURLOPT_COOKIEFILE, $data['cookie_file']);
            if(!empty($data['save_cookie'])) curl_setopt($ch, CURLOPT_COOKIEJAR, $data['save_cookie']);
            if(!empty($data['proxy'])) curl_setopt($ch, CURLOPT_PROXY, $data['proxy']);
            
            if(!empty($data['post']))
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
            }
            
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $data['timeout']);
            curl_setopt($ch, CURLOPT_TIMEOUT, $data['timeout']);
            if(!empty($data['option']))
            {
                curl_setopt_array($ch, $data['option']);
            }
            
            if(!empty($data['return_curl'])) return $ch;
            
            $ret = curl_exec($ch);
            
            $errno = curl_errno($ch);//var_dump($errno);
            
            $header = curl_getinfo($ch);
            if( !empty($data['return_head']) ) 
            {
                return $header;
            }
            
            if($errno)
            {
                $error = curl_error($ch);
                curl_close($ch);
                
                $s = "$data[url]|$errno|$error";
                log::error($s);
                
                return array('head' => $header, 'body' => null, 'info' => array(
                    'errno' => $errno,
                    'error' => $error
                ));
            }
            
            //$tmp = explode("\r\n\r\n", $ret, 2);
            //print_r($ret);
            //unset($ret);
            
            $info = curl_getinfo($ch);
            
            curl_close($ch);
            
            return array('head' => $header, 'body' => $ret, 'info' => array(
                'status' => $info['http_code']
            ));
        }
    }

    /**
     * 合并数组，多维不会覆盖
     * array_merge_multiple(array1, array2 ....)
     * @return array
     */
    public static function array_merge_multiple()
    {
        $arrays = func_get_args();
        $merged = [];
        while ($arrays) 
        {
            $array = array_shift($arrays);
            if (!is_array($array)) 
            {
                trigger_error(__FUNCTION__ .' encountered a non array argument', E_USER_WARNING);
                return;
            }

            if ( !$array ) continue;
            foreach ($array as $key => $value)
            {
                if (is_string($key))
                {
                    if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                    {
                        $merged[$key] = call_user_func([__CLASS__, __FUNCTION__], $merged[$key], $value);
                    }
                    else
                    {
                        $merged[$key] = $value;
                    }
                }
                else
                {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}

/* vim: set expandtab: */

