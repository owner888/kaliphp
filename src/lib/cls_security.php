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
use kaliphp\config;
use kaliphp\req;
use kaliphp\util;
use kaliphp\log;

/** 
 * 安全类，XSS、CSRF 
 *
 * @version $Id$  
 */
class cls_security
{
    private static $config = [];
    private static $cookie_config = [];

    /**
     * XSS Hash
     *
     * Random Hash for protecting URLs.
     *
     * @var	string
     */
    private static $_xss_hash;

    /**
     * CSRF Hash
     *
     * Random hash for Cross Site Request Forgery protection cookie
     *
     * @var	string
     */
    private static $_csrf_hash;

    private static $_csrf_token_on = false;

    private static $_csrf_token_reset = true;

    /**
     * CSRF Expire time
     *
     * Expiration time for Cross Site Request Forgery protection cookie.
     * Defaults to two hours (in seconds).
     *
     * @var	int
     */
    private static $_csrf_expire =	7200;

    /**
     * CSRF Token name
     *
     * Token name for Cross Site Request Forgery protection cookie.
     *
     * @var	string
     */
    private static $_csrf_token_name =	'csrf_token_name';

    /**
     * CSRF Cookie name
     *
     * Cookie name for Cross Site Request Forgery protection cookie.
     *
     * @var	string
     */
    private static $_csrf_cookie_name =	'csrf_cookie_name';

    private static $_csrf_exclude_uris = [];

    public static function _init()
    {
        self::$config = config::instance('app_config')->get('security');
        self::$cookie_config = config::instance('config')->get('cookie');
        //print_r(self::$cookie_config);
        //print_r(req::$config);
        //print_r(self::$config);
        //exit;
        // Is CSRF protection enabled?
        if (req::$config['csrf_token_on'])
        {
            // CSRF config
            foreach (array('csrf_token_on', 'csrf_token_name', 'csrf_token_reset', 'csrf_cookie_name', 'csrf_expire', 'csrf_exclude_uris') as $key)
            {
                if (NULL !== ($val = req::$config[$key]))
                {
                    self::${'_'.$key} = $val;
                }
            }

            // Set the CSRF hash
            self::_csrf_set_hash();
        }

    }

    public static function xss_clean($str, $is_image = FALSE)
    {
        // Is the string an array?
        if (is_array($str))
        {
            foreach ($str as $key => &$value)
            {
                $str[$key] = self::xss_clean($value);
            }

            return $str;
        }

        $str = util::remove_invisible_characters($str);

        if ($is_image === TRUE)
        {
            $converted_string = $str;
            $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
            return ($str === $converted_string);
        }
        else
        {
            $str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);
        }

        $str = htmlspecialchars($str);

        $str = util::remove_invisible_characters($str);

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * 判断子网掩码是否一致
     * @param $addr
     * @param $cidr
     * @return bool
     */
    public static function match_cidr($addr, $cidr) 
    {
        list($ip, $mask) = explode('/', $cidr);
        return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
    }

    /**
     * 验证 CSRF Token
     *
     * @return	bool
     */
    public static function csrf_verify()
    {
        // CSRF关闭
        if ( !self::$_csrf_token_on || in_array(req::method(), ['CLI', 'HEAD', 'OPTIONS']) )
        {
            return true;
        }

        // Check if IP has been whitelisted from CSRF checks
        $ips = req::$config['csrf_white_ips'];
        foreach ($ips as $ip)
        {
            if (self::match_cidr(req::ip(), $ip))
            {
                return true;
            }
        }

        // Check if URI has been whitelisted from CSRF checks
        if ($exclude_uris = self::$_csrf_exclude_uris)
        {
            foreach ($exclude_uris as $excluded)
            {

                if (preg_match('#^'.$excluded.'#iu', req::query_string()))
                {
                    return true;
                }
            }
        }

        // If it's a GET request we will set the CSRF cookie
        if ( req::method() === 'GET')
        {
            return self::csrf_set_cookie();
        }

        $csrf_token = null;
        $csrf_cookie = req::cookie(self::$_csrf_cookie_name);
        if ( req::is_ajax() )    
        {
            $csrf_token = req::server('HTTP_X_CSRF_TOKEN');
        }
        else 
        {
            $csrf_token = req::post(self::$_csrf_token_name);
            unset(req::$posts[self::$_csrf_token_name]);
        }

        // Check CSRF token validity, but don't error on mismatch just yet - we'll want to regenerate
        $valid = isset($csrf_token, $csrf_cookie)
            && hash_equals($csrf_token, $csrf_cookie);

        // Regenerate on every submission?
        if (self::$_csrf_token_reset)
        {
            // Nothing should last forever
            unset($_COOKIE[self::$_csrf_cookie_name]);
            self::$_csrf_hash = NULL;
        }

        self::_csrf_set_hash();
        self::csrf_set_cookie();

        if ($valid !== true)
        {
            self::csrf_show_error();
        }

        //log::info('CSRF token verified');
        return true;
    }

    // --------------------------------------------------------------------

    /**
     * CSRF Set Cookie
     *
     * @codeCoverageIgnore
     * @return	CI_Security
     */
    public static function csrf_set_cookie()
    {
        $expire = time() + self::$_csrf_expire;

        $secure_cookie = (bool) self::$cookie_config['secure'];

        if ($secure_cookie && ! util::is_https())
        {
            return false;
        }

        setcookie(
            self::$_csrf_cookie_name,
            self::$_csrf_hash,
            $expire,
            self::$cookie_config['path'],
            self::$cookie_config['domain'],
            $secure_cookie,
            self::$cookie_config['httponly']
        );

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Show CSRF Error
     *
     * @return	void
     */
    public static function csrf_show_error()
    {
        log::error(IP . " - " . req::query_string(), 'CSRF Error');
        cls_msgbox::error('500');
    }

    // --------------------------------------------------------------------

    /**
     * Get CSRF Hash
     *
     * @see		CI_Security::$_csrf_hash
     * @return 	string	CSRF hash
     */
    public static function get_csrf_hash()
    {
        return self::$_csrf_hash;
    }

    // --------------------------------------------------------------------

    /**
     * Get CSRF Token Name
     *
     * @see		CI_Security::$_csrf_token_name
     * @return	string	CSRF token name
     */
    public static function get_csrf_token_name()
    {
        return self::$_csrf_token_name;
    }

    // --------------------------------------------------------------------

    private static function _csrf_set_hash()
    {
        if (self::$_csrf_hash === NULL)
        {
            // If the cookie exists we will use its value.
            // We don't necessarily want to regenerate it with
            // each page load since a page could contain embedded
            // sub-pages causing this feature to fail
            if (isset($_COOKIE[self::$_csrf_cookie_name]) && is_string($_COOKIE[self::$_csrf_cookie_name])
                && preg_match('#^[0-9a-f]{32}$#iS', $_COOKIE[self::$_csrf_cookie_name]) === 1)
            {
                return self::$_csrf_hash = $_COOKIE[self::$_csrf_cookie_name];
            }

            $rand = self::get_random_bytes(16);
            self::$_csrf_hash = ($rand === false)
                ? md5(uniqid(mt_rand(), true))
                : bin2hex($rand);
            //self::$_csrf_hash = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'] . self::$_csrf_hash);
        }

        return self::$_csrf_hash;
    }

    /**
     * Get random bytes
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public static function get_random_bytes($length)
    {
        if (empty($length) OR ! ctype_digit((string) $length))
        {
            return FALSE;
        }

        if (function_exists('random_bytes'))
        {
            try
            {
                // The cast is required to avoid TypeError
                return random_bytes((int) $length);
            }
            catch (Exception $e)
            {
                // If random_bytes() can't do the job, we can't either ...
                // There's no point in using fallbacks.
                log::error($e->getMessage());
                return FALSE;
            }
        }

        // Unfortunately, none of the following PRNGs is guaranteed to exist ...
        if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== FALSE)
        {
            return $output;
        }


        if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== FALSE)
        {
            // Try not to waste entropy ...
            version_compare(PHP_VERSION, '5.4', '>=') && stream_set_chunk_size($fp, $length);
            $output = fread($fp, $length);
            fclose($fp);
            if ($output !== FALSE)
            {
                return $output;
            }
        }

        if (function_exists('openssl_random_pseudo_bytes'))
        {
            return openssl_random_pseudo_bytes($length);
        }

        return FALSE;
    }

    /**
     * spam函数
     * @param  array
     * cls_security::spam(array(
     *     'key' => 键名，获取数据的时候只要传key就可以了
     *     'data' => 需要保存的数据,
     *     'action' => 默认get 保存为save,清除为clear
     * ));
     * @return mix
     */
    public static function spam($data)
    {
        $prefix = 'sys_spam';
        static $spams = array();
        $data = array_merge(array('key' => '', 'action' => 'check'), $data);

        $key = 'spam|'. $data['key'];
        if( !isset($spams[$key]) )
        {
            $spams[$key] = cache::get($prefix, $key);
            if(empty($spams[$key]))
            {
                $spams[$key] = array(
                    'total' => 0
                );
            }
        }

        if( $data['action'] == 'save' )
        {
            $spams[$key]['total']++;
            $spams[$key]['timestamp'] = TIMESTAMP;
            if(isset($data['data']))
            {
                $spams[$key]['data'] = $data['data'];
            }

            return cache::set($prefix, $key, $spams[$key]);
        }
        else if( $data['action'] == 'clear' )
        {
            unset($spams[$key]);
            return cache::set($prefix, $key, array());
        }

        return $spams[$key];
    }

    /**
     * IP过滤器
     * 
     * @return void
     */
    public static function ip_filter()
    {
        // 如果IP在黑名单里面，而且不在白名单里面
        if ( in_array(IP, self::$config['ip_blacklist']) && !in_array(IP, self::$config['ip_whitelist']) )
        {
            exit(header("HTTP/1.1 404 Not Found"));
        }
    }

    /**
     * 国家过滤器
     * 
     * @return void
     */
    public static function country_filter()
    {
        // 如果IP在黑名单里面，而且不在白名单里面
        if ( in_array(COUNTRY, self::$config['country_blacklist']) && !in_array(COUNTRY, self::$config['country_whitelist']) )
        {
            // 国家有时候又判断错误的情况，可以把这些判断错误的IP过滤
            if ( in_array(IP, self::$config['ip_whitelist']) ) 
            {
                return true;
            }
            exit(header("HTTP/1.1 404 Not Found"));
        }
    }
}
