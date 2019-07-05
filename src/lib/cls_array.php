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

class cls_array
{
    /**
     * 二维数组去重
     * 
     * @param array $arr
     * @param mixed $key
     * @return array
     */
    public static function array_unset_tt( array $arr, $key )
    {
        // 建立一个目标数组
        $res = array();
        foreach ($arr as $value) 
        {
            // 查看有没有重复项
            if (isset($res[$value[$key]])) 
            {
                // 有：销毁
                unset($value[$key]);
            } 
            else 
            {
                $res[$value[$key]] = $value;
            }
        }
        return $res;
    }
}
