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

use kaliphp\config;

/** 
 * 时间工具类
 *
 * @version $Id$  
 */
class cls_time
{
    /**
     * 不同时区时间转换
     *
     * @param array $data
     *      pub_func::time_convert([
     *      'datetime'      => FRAME_TIMESTAMP,//可以是时间格式或者时间戳
     *      'from_timezone' => 'ETC/GMT-7',//默认为系统设置的时区，即 ETC/GMT
     *      'to_timezone'   => 'ETC/GMT-8',//转换成为的时区，默认获取用户所在国家对应时区
     *      'format'        => ''//格式化输出字符串。默认为Y-m-d H:i:s
     *      'default'       => 表示没有为空的时候，默认实现的字符串，默认为-
     *      ]);
     *
     * 一般直接使用 pub_func::time_convert(['datetime' => xxxxx]);
     * @return string
     */
    public static function time_convert(array $data = []): string
    {
        $data_default = [
            'datetime'      => FRAME_TIMESTAMP,
            'format'        => 'Y-m-d H:i',
            'from_timezone' => null,
            'to_timezone'   => null,
        ];

        $configs = [];
        foreach ($data_default as $f => $ff)
        {
            $configs[$f] = isset($data[$f]) ? $data[$f] : $ff;
        }

        $default = isset($data['default']) ? $data['default'] : '-';
        if (empty($data['datetime']))
        {
            return $default;
        }

        $configs['to_timezone'] = empty($configs['to_timezone']) ? config::instance('config')->get('to_timezone') : $configs['to_timezone'];

        return call_user_func_array(['kaliphp\util', 'to_timezone'], $configs);
    }

    /**
     * 返回时间戳剩余 时分秒
     * 
     * @param int $time1
     * @param int $time2
     * @return string
     */
    public static function time_string(int $time1, int $time2 = FRAME_TIMESTAMP): string
    {
        $second = $time2-$time1;
        $day = floor($second/(3600*24));
        $second = $second%(3600*24); // 除去整天之后剩余的时间
        $hour = floor($second/3600);
        $second = $second%3600;      // 除去整小时之后剩余的时间
        $minute = floor($second/60);
        $second = $second%60;        // 除去整分钟之后剩余的时间

        // 返回字符串
        return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
    }

    /**
     * 秒数转时间
     *
     * @param int $seconds 秒数
     *
     * @return string
     */
    public static function convert_seconds_to_time($seconds): string
    {
        $lang = empty($lang) ? (defined('LANG') ? LANG : 'zh-cn') : $lang;

        $time_str = '';
        if ($seconds)
        {
            $str_sec   = intval($seconds % 60);
            $total_min = $seconds / 60;
            $str_min   = intval($total_min % 60);
            $str_hour  = intval($total_min / 60);

            if ( $str_hour )
            {
                $lang_data = ['zh-cn' => '小时', 'en' => 'Hour'];
                $time_str .= "{$str_hour}" . $lang_data[$lang];
            }
            if ( $str_min )
            {
                $lang_data = ['zh-cn' => '分', 'en' => 'Min'];
                $time_str .= "{$str_min}" . $lang_data[$lang];
            }
            if ( $str_sec )
            {
                $lang_data = ['zh-cn' => '秒', 'en' => 'Seconds'];
                $time_str .= "{$str_sec}" . $lang_data[$lang];
            }
        }

        return $time_str;
    }

    /**
     * 日期转时间戳
     *
     * @param string $date     日期
     * @param string $timezone 转换时区
     *
     * @return int
     */
    public static function date_convert_timestamp($date, $timezone = null): int
    {
        // 如果没有传时区，用国家代码从配置文件获取对应时区
        $timezone = empty($timezone) ? config::instance('config')->get(COUNTRY) : $timezone;
        // 配置文件也没有找到时区，使用默认配置的时区
        $timezone = empty($timezone) ? config::instance('config')->get('to_timezone') : $timezone;
        if (empty($timezone))
        {
            return strtotime($date);
        }

        $timezone = new \DateTimeZone($timezone);
        $date_obj = new \DateTime($date, $timezone);
        $time     = $date_obj->format('U');

        return $time;
    }

    /**
     * 获取两个时区相差的秒数
     *
     * @param string|null $timezone1
     * @param string|null $timezone2
     *
     * @return int 相差秒数
     */
    public static function timezone_diff_seconds(?string $timezone1 = null, ?string $timezone2 = null): int
    {
        $timezone1 = empty($timezone1) ? config::instance('config')->get('timezone_set') : $timezone1;
        // 如果没有传时区，用国家代码从配置文件获取对应时区
        $timezone2 = empty($timezone2) ? config::instance('config')->get(COUNTRY) : $timezone2;
        // 配置文件也没有找到时区，使用默认配置的时区
        $timezone2 = empty($timezone2) ? config::instance('config')->get('to_timezone') : $timezone2;

        if (empty($timezone1) || empty($timezone2) || $timezone1 === $timezone2) return 0;

        $date  = date('YmdH:i:s');
        $time1 = self::date_convert_timestamp($date, $timezone1);
        $time2 = self::date_convert_timestamp($date, $timezone2);

        return $time1 - $time2;
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     *
     * @param string $from_day
     * @param string $to_day
     * 
     * @return int
     */
    public static function diff_between_days(string $from_day, string $to_day): int
    {
        $from_time = strtotime($from_day);
        $to_time   = strtotime($to_day);

        if ($from_time < $to_time)
        {
            $tmp       = $to_time;
            $to_time   = $from_time;
            $from_time = $tmp;
        }
        return intval(($from_time - $to_time) / 86400);
    }

    /**
     * 获取一周的开始和结束时间
     *
     * @param int $week
     * @param int $year
     * 
     * @return array
     */
    public static function get_week_range(int $week, int $year): array
    {
        $date_obj = new \DateTime();
        $date_obj->setISODate($year, $week);
        $ret['start'] = $date_obj->format('Y-m-d');

        $date_obj->modify('+6 days');
        $ret['end'] = $date_obj->format('Y-m-d');
        return $ret;
    }

    /**
     * 获取某一个月的开始和结束日期
     *
     * @param string $date
     * 
     * @return array
     */
    public static function get_month_range(string $date): array
    {
        $first_day = date('Y-m-01', strtotime($date));
        $last_day  = date('Y-m-d', strtotime("{$first_day} +1 month -1 day"));
        return ['start' => $first_day, 'end' => $last_day];
    }
}
