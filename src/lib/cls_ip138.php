<?php

namespace kaliphp\lib;

/**
 * ip138 解析库
 * 
 *  ip地址转换地理信息
 *  $raddrArr = cls_ip138::instance()->ip_to_address('1.0.2.1');
 *  返回结果结构
 *      ["country" =>'',"province" => '',"city" => ''];
 * 
 */
class cls_ip138 
{
    protected static $_instance = null;
    private static $db = null;
    private static $offset = 0;
    private static $total = 0;

    public static function instance() 
    {
        if (!self::$_instance instanceof self) 
        {
            self::$_instance = new self();
            self::init();
        }

        return self::$_instance;
    }

    private static function init() 
    {
        if (self::$db === null) 
        {
            $filepath = APPPATH . '/../../../../ip138.dat';
            if ( !file_exists($filepath)) 
            {
                exit($filepath . ' not exist.!');
                throw new \Exception($filepath . ' not exist.!');
            }
            self::$db = fopen(($filepath), 'rb');
            if (self::$db === false) 
            {
                throw new \Exception('Invalid ' . $filepath . ' file!');
            }
        }
    }

    public static function query(string $ip) 
    {
        if (empty($ip)) 
        {
            return 'N/A';
        }
        // 域名获取 IP
        $ip = gethostbyname($ip);
        if (!preg_match('#^(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}$#', $ip)) 
        {
            return 'N/A';
        }

        $iplong = ip2long($ip);
        // 初始化
        if (self::$db === null) 
        {
            self::init();
        }

        return self::find($iplong);
    }

    private static function find(string $iplong) 
    {
        // 文本偏移量（4字节） + 索引区（256*4字节，第一段开始的记录位置） + 记录区（4字节结束ip+4字节文本偏移量+1字节文本长度） + 数据区
        fseek(self::$db, 0);
        self::$offset = unpack('I', fread(self::$db, 4))[1];

        // ip段的数量
        self::$total = (self::$offset - 4 - 256 * 4) / 9;

        // 分割索引值，abc.def.igh.lkm，为加快索引增加abc分割
        $first = $iplong >> 24;
        if ($first == 0) 
        {
            fseek(self::$db, 4 + ($first) * 4);
            $left = unpack('I', fread(self::$db, 4))[1];
            fseek(self::$db, 4 + ($first + 1) * 4);
            $right = unpack('I', fread(self::$db, 4))[1] - 1;
        } 
        elseif ($first == 255) 
        {
            fseek(self::$db, 4 + ($first - 1) * 4);
            $left = unpack('I', fread(self::$db, 4))[1] + 1;
            $right = self::$total;
        } 
        else 
        {
            fseek(self::$db, 4 + ($first) * 4);
            $left = unpack('I', fread(self::$db, 4))[1];
            fseek(self::$db, 4 + ($first + 1) * 4);
            $right = unpack('I', fread(self::$db, 4))[1] - 1;
            if ($right < 1) 
            {
                $right = self::$total;
            }
        }

        // 读取各ip段数据（结束值、所在文本偏移值、所在文本长度）
        $i    = 0;
        $text = 'N/A';
        while ($i < 24) 
        {
            // 2^24二叉树查找最多16777216条数据
            $middle = floor(($left + $right) / 2);
            if ($middle == $left) 
            {
                $ipOffset = 4 + 256 * 4 + $right * 9;
                fseek(self::$db, $ipOffset + 4);
                $offset = unpack('I', fread(self::$db, 4))[1];
                fseek(self::$db, $ipOffset + 4 + 4);
                $text_length = unpack('C', fread(self::$db, 1))[1];
                // var_dump(long2ip($middleIplong).' = '.$middle.' = '.$ipOffset);

                fseek(self::$db, self::$offset + $offset);
                $text = fread(self::$db, $text_length);
                break;
            }

            $middle_offset = 4 + 256 * 4 + $middle * 9;
            fseek(self::$db, $middle_offset);
            $middleIplong = unpack('I', fread(self::$db, 4))[1];
            if ( $iplong <= $middleIplong ) 
            {
                $right = $middle;
            } 
            else 
            {
                $left = $middle;
            }
            $i++;
        }

        return $text;
    }
    
    /**
     * ip地址转换地理信息
     * 
     * @param  string  $ip 8.8.8.8
     * @return array   ["country" =>'',"province" => '',"city" => '']
     */
    public static function ip_to_address(string $ip) 
    {
         $full_info = self::query($ip);
         $arr_info  = explode(chr(9), $full_info);
         return [
            "countryName" => $arr_info[0] ?? '',
            "regionName"  => $arr_info[1] ?? '',
            "cityName"    => $arr_info[2] ?? ''
        ];
    }
    
    /**
     * 返回地区字符串
     */
    public static function get_ip_address(string $ip)
    {
        $arr_area = self::ip_to_address($ip);
        if ( !is_array($arr_area) )
        {
            return $arr_area;
        }

        $arr_area = array_unique($arr_area);
        $arr_area = array_filter($arr_area);
        return implode('', $arr_area);
    }

    public function __destruct() 
    {
        if ( self::$db !== null && is_resource(self::$db) ) 
        {
            fclose(self::$db);
        }
    }

}
