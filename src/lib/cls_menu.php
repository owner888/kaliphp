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
use kaliphp\kali;
use kaliphp\util;
use kaliphp\lang;
use Exception;

/**
 * UI框架栏目 
 * 
 * @version 2.7.0
 */
class cls_menu
{
    public static $menu_file = 'menu.xml';
    public static $menu_data = null;
    public static $menu_json = null;
    public static $apps = array();
    public static $is_auth = true;
    public static $is_hide = true;

    public static function _init()
    {
        $menu_file = isset(kali::$config['menu_file']) ? kali::$config['menu_file'] : self::$menu_file;
        self::$menu_file = APPPATH.DS.'config'.DS.$menu_file;
        self::$menu_data = util::get_file(self::$menu_file);
        if ( empty(self::$menu_data)) 
        {
            throw new Exception(self::$menu_file.' not found');
        }

    }

    public static function parse_menu( $replace_menu_data = array() )
    {
        // 替换栏目不为空，进行替换
        if ( !empty($replace_menu_data) ) 
        {
            foreach ($replace_menu_data as $k=>$v) 
            {
                // <server_menu />
                $replace_menu = "<{$k} />";
                if (strpos(self::$menu_data, $replace_menu) !== false)
                {
                    $tpl_menu = self::array_to_xml_menu($v);
                    self::$menu_data = str_replace($replace_menu, $tpl_menu, self::$menu_data);
                }
            }
        }

        self::$menu_data = self::replace_app_menu(self::$menu_data);
        $values = self::xml_to_array(self::$menu_data);
        $values = self::set_parentid($values);
        self::$menu_json = json_encode($values, JSON_UNESCAPED_UNICODE);
        self::$menu_json = lang::tpl_change(self::$menu_json);
        return self::$menu_json;
    }

    /** 
     * 通过数组生成menu 
     * @param  string   $xml xml字符串 
     **/  
    public static function array_to_xml_menu($data)
    {  
        $xml = '';
        foreach ($data as $key=>$val)
        {  
            $xml .= '<menu';
            foreach ($val as $k=>$v) 
            {
                if ( empty($v)) 
                {
                    continue;
                }
                $xml .= ' '.$k.'="'.$v.'"';
            }
            $xml .= " />\n";
        }  
        return $xml;   
    } 

    /**
     * 获得权限
     * 
     * @param bool $is_auth    是否验证权限
     * @param bool $is_hide    是否过滤 display='none' 栏目
     * @return string   JSON
     */
    public static function get_purviews($is_auth = false, $is_hide = false)
    {
        self::$is_auth = $is_auth;
        self::$is_hide = $is_hide;
        $menu_json = self::parse_menu();
        return $menu_json;
    }

    public static function set_parentid($arr)
    {
        foreach ($arr as $k => $em) 
        {
            $display = !empty($em['display']) ? $em['display'] : '';
            if ( $display == 'none' ) 
            {
                unset($arr[$k]);
                continue;
            }
            $em = self::_set_parentid($em);
            if ( empty($em) ) 
            {
                unset($arr[$k]);
                continue;
            }

            $arr[$k] = $em;
        }
        $arr = array_values($arr);
        return $arr;
    }

    private static function _set_parentid($arr, $parentid = 0, $topid = 0)
    {
        $arr['parentid'] = $parentid;
        $arr['topid']    = $topid;
        if ( $parentid==0 ) $topid = $arr['id'];
        // 如果不存在子节点，说明是末梢节点
        if ( !isset($arr['children']) ) 
        {
            $ct      = !empty($arr['ct']) ? $arr['ct'] : '';
            $ac      = !empty($arr['ac']) ? $arr['ac'] : '';
            $display = !empty($arr['display']) ? $arr['display'] : '';
            // 去掉ct和ac为空的栏目
            if ( empty($ct) || empty($ac)) 
            {
                return null;
            }
            if ( self::$is_hide && $display=='none' ) 
            {
                return null;
            }
            // 如果有做验证 而且 验证权限不通过，也去掉
            if ( self::$is_auth && !self::_has_purview($ct, $ac) ) 
            {
                return null;
            }
            if (empty($arr['url'])) 
            {
                $arr['url'] = "?ct={$ct}&ac={$ac}";
            }
            return $arr;
        }

        // 如果存在子节点，进入子节点递归处理
        foreach ($arr['children'] as $k => $son) 
        {
            $child = self::_set_parentid($son, $arr['id'], $topid);
            if (empty($child)) 
            {
                // 当前子栏目为空，去掉此节点
                unset($arr['children'][$k]);
            }
            else 
            {
                $arr['children'][$k] = $child;
            }
        }

        // 去完没有权限的子栏目以后，子栏目为空，干掉整个节点
        if (empty($arr['children'])) 
        {
            return null;
        }
        $arr['children'] = array_values($arr['children']);
        return $arr;
    }

    public static function xml_to_array($xml) 
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $tags);
        xml_parser_free($parser);

        $elements = array();  // the currently filling [child] array
        $stack = array();
        foreach ($tags as $tag) 
        {
            $index = count($elements);
            if ($tag['type'] == "complete" || $tag['type'] == "open") 
            {
                $elements[$index] = array();
                //$elements[$index]['name'] = $tag['tag'];
                $elements[$index]['id'] = 0;
                $elements[$index]['parentid'] = 0;
                $elements[$index]['topid'] = 0;
                if (isset($tag['attributes'])) 
                {
                    $elements[$index] = array_merge($elements[$index], $tag['attributes']);
                }
                if ($tag['type'] == "open") 
                {  
                    // push
                    $elements[$index]['children'] = array();
                    $stack[count($stack)] = &$elements;
                    $elements = &$elements[$index]['children'];
                }
            }
            if ($tag['type'] == "close") 
            {  
                // pop
                $elements = &$stack[count($stack) - 1];
                unset($stack[count($stack) - 1]);
            }
        }
        // xml的不需要
        return $elements[0]['children'];
    }

    /**
     * 处理应用菜单的操作
     * @param string $menu
     * @return bool
     */
    public static function replace_app_menu( $xml )
    {
        $lines = explode("\n", $xml);
        $i = 0;
        foreach ($lines as $line) 
        {
            // 空的和注释的行去掉
            if (empty(trim($line)) || preg_match("#^<!--#", trim($line)))
            {
                continue;
            }

            if (preg_match("#<menu #", $line))
            {
                $i++;
                $line = str_replace("<menu", "<menu id='{$i}'", $line);
            }
            $menu_array[] = $line;
        }
        $xml = implode("\n", $menu_array);
        $xml = str_replace("&amp;", "&", $xml);
        $xml = str_replace("&", "&amp;", $xml);
        return $xml;
    }

    /**
     * 检测用户是否有指定权限
     * @param string $ct
     * @param string $ac
     * @return bool
     */
    protected static function _has_purview($ct, $ac)
    {
        $rs = kali::$auth->check_purview($ct, $ac, 2);
        if ( $rs == 1 )
        {
            return true;
        } 
        else
        {
            return false;
        }
    }

}
