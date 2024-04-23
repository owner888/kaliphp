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

namespace kaliphp\lib;

/**
 * PHP Class for handling Google Authenticator 2-factor authentication
 *
 * https://github.com/PHPGangsta/GoogleAuthenticator
 * https://github.com/Dolondro/google-authenticator/tree/master/src
 *
 * @author Michael Kliewe
 * @copyright 2012 Michael Kliewe
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class cls_google_auth
{
    private static $_code_length = 6;

    /**
     * Create new secret.
     * 16 characters, randomly chosen from the allowed base32 characters.
     *
     * @param int $secret_length
     * @return string
     */
    public static function create_secret($secret_length = 16)
    {
        $valid_chars = self::_get_base32_lookup_table();
        unset($valid_chars[32]);

        $secret = '';
        for ($i = 0; $i < $secret_length; $i++) 
        {
            $secret .= $valid_chars[array_rand($valid_chars)];
        }
        return $secret;
    }

    /**
     * Calculate the code, with given secret and point in time
     *
     * @param string $secret
     * @param int|null $time_slice
     * @return string
     */
    public static function get_code($secret, $time_slice = null)
    {
        if ($time_slice === null) 
        {
            $time_slice = floor(time() / 30);
        }

        $secretkey = self::_base32_decode($secret);

        // Pack time into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $time_slice);
        // Hash it with users secret key
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        // Use last nipple of result as index/offset
        $offset = ord(substr($hm, -1)) & 0x0F;
        // grab 4 bytes of the result
        $hashpart = substr($hm, $offset, 4);

        // Unpak binary value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        // Only 32 bits
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, self::$_code_length);
        return str_pad($value % $modulo, self::$_code_length, '0', STR_PAD_LEFT);
    }

    /**
     * Get QR-Code URL for image, from google charts.
     *
     * @param string $name
     * @param string $secret
     * @param string $title
     * @param array  $params
     *
     * @return string
     */
    public static function get_qrcode_url($name, $secret, $title = null, $params = array())
    {
        $width = !empty($params['width']) && (int) $params['width'] > 0 ? (int) $params['width'] : 200;
        $height = !empty($params['height']) && (int) $params['height'] > 0 ? (int) $params['height'] : 200;
        $level = !empty($params['level']) && array_search($params['level'], array('L', 'M', 'Q', 'H')) !== false ? $params['level'] : 'M';

        $urlencoded = 'otpauth://totp/'.$name.'?secret='.$secret.'';
        if (isset($title))
        {
            $urlencoded .= '&issuer='.$title;
        }

        return $urlencoded;
        // 
        // 被google启用了
        // $urlencoded = urlencode('otpauth://totp/'.$name.'?secret='.$secret.'');
        // if (isset($title)) 
        // {
        //     $urlencoded .= urlencode('&issuer='.urlencode($title));
        // }
        // return 'https://chart.googleapis.com/chart?chs='.$width.'x'.$height.'&chld='.$level.'|0&cht=qr&chl='.$urlencoded.'';
    }

    /**
     * Check if the code is correct. This will accept codes starting from $discrepancy*30sec ago to $discrepancy*30sec from now
     *
     * @param string $secret
     * @param string $code
     * @param int $discrepancy 允许的30秒单位的时间漂移（8是8*30=240秒，表示4分钟之前或之后），但是这里实际是前后8秒，貌似有点问题
     * @param int|null $cur_time_slice time slice if we want use other that time()
     * @return bool
     */
    public static function verify_code($secret, $code, $discrepancy = 1, $cur_time_slice = null)
    {
        if ($cur_time_slice === null) 
        {
            $cur_time_slice = floor(time() / 30);
        }

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) 
        {
            $calculated_code = self::get_code($secret, $cur_time_slice + $i);
            if ( self::_timing_safe_equals($calculated_code, $code) ) 
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the code length, should be >=6
     *
     * @param int $length
     * @return cls_google_auth
     */
    public static function set_code_length($length)
    {
        self::$_code_length = $length;
    }

    /**
     * Helper class to decode base32
     *
     * @param $secret
     * @return bool|string
     */
    private static function _base32_decode($secret)
    {
        if (empty($secret)) return '';

        $base32chars = self::_get_base32_lookup_table();
        $base32chars_flipped = array_flip($base32chars);

        $padding_char_count = substr_count($secret, $base32chars[32]);
        $allowed_values = array(6, 4, 3, 1, 0);
        if (!in_array($padding_char_count, $allowed_values)) return false;
        for ($i = 0; $i < 4; $i++)
        {
            if ($padding_char_count == $allowed_values[$i] &&
                substr($secret, -($allowed_values[$i])) != str_repeat($base32chars[32], $allowed_values[$i])) return false;
        }
        $secret = str_replace('=','', $secret);
        $secret = str_split($secret);
        $bin_str = "";
        for ($i = 0; $i < count($secret); $i = $i+8) 
        {
            $x = "";
            if (!in_array($secret[$i], $base32chars)) return false;
            for ($j = 0; $j < 8; $j++) 
            {
                $x .= str_pad(base_convert(@$base32chars_flipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eight_bits = str_split($x, 8);
            for ($z = 0; $z < count($eight_bits); $z++) 
            {
                $bin_str .= ( ($y = chr(base_convert($eight_bits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $bin_str;
    }

    /**
     * Helper class to encode base32
     *
     * @param string $secret
     * @param bool $padding
     * @return string
     */
    private static function _base32_encode($secret, $padding = true)
    {
        if (empty($secret)) return '';

        $base32chars = self::_get_base32_lookup_table();

        $secret = str_split($secret);
        $bin_str = "";
        for ($i = 0; $i < count($secret); $i++) 
        {
            $bin_str .= str_pad(base_convert(ord($secret[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $five_bit_bin_arr = str_split($bin_str, 5);
        $base32 = "";
        $i = 0;
        while ($i < count($five_bit_bin_arr)) 
        {
            $base32 .= $base32chars[base_convert(str_pad($five_bit_bin_arr[$i], 5, '0'), 2, 10)];
            $i++;
        }
        if ($padding && ($x = strlen($bin_str) % 40) != 0) 
        {
            if ($x == 8) $base32 .= str_repeat($base32chars[32], 6);
            elseif ($x == 16) $base32 .= str_repeat($base32chars[32], 4);
            elseif ($x == 24) $base32 .= str_repeat($base32chars[32], 3);
            elseif ($x == 32) $base32 .= $base32chars[32];
        }
        return $base32;
    }

    /**
     * Get array with all 32 characters for decoding from/encoding to base32
     *
     * @return array
     */
    private static function _get_base32_lookup_table()
    {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '='  // padding char
        );
    }

    /**
     * A timing safe equals comparison
     * more info here: http://blog.ircmaxell.com/2014/11/its-all-about-time.html.
     *
     * @param string $safe_str The internal (safe) value to be checked
     * @param string $user_str The user submitted (unsafe) value
     *
     * @return bool True if the two strings are identical
     */
    private static function _timing_safe_equals($safe_str, $user_str)
    {
        if (function_exists('hash_equals')) 
        {
            return hash_equals($safe_str, $user_str);
        }

        $safe_len = strlen($safe_str);
        $user_len = strlen($user_str);
        if ($user_len != $safe_len) 
        {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $user_len; ++$i) 
        {
            $result |= (ord($safe_str[$i]) ^ ord($user_str[$i]));
        }
        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }
}
