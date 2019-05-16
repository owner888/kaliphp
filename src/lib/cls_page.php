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
use kaliphp\req;

/**
 * 分页类
 *
 * @version $Id$  
 */
class cls_page
{
    public static function make($total_rs, $page_size = 10, $page_name = 'page_no', $move_size = 5, $jump_info = true, $use_info = true)
    {
        // 拼凑分页连接地址
        $prefix = req::$gets;

        // 每页显示条数
        //$page_size = isset($page_size) ? $page_size : req::item('page_size', 20);
        // 默认调用初始化的页数，当用户选择了页数，就以用户选择为主
        $page_size = isset($prefix['page_size']) ? req::item('page_size', 20) : $page_size;
        unset($prefix[$page_name]);
        $url_prefix = '?'.http_build_query($prefix);

        // 当前页数,至少为1
        $current_page = req::item($page_name, 1);
        $offset = ( $current_page - 1 ) * $page_size;

        $total_page = ceil($total_rs / $page_size);

        // 如果当前页大于总页数
        if ($total_page > 0 && $current_page > $total_page)
        {
            // 如果只有一页数据，就不带分页参数了
            if ($total_page == 1)
            {
                unset($prefix[$page_name]);
            }
            else
            {
                $prefix[$page_name] = $total_page;
            }
            $jump_url = '?'.http_build_query($prefix);
            header("Location: {$jump_url}");
            exit;
        }

        // 总页数不到二页返回空
        if ($total_page < 1)
        {
            return array(
                'show'      => '',
                'page_size' => $page_size,
                'offset'    => $offset,
            );
        }

        // 分页内容
        $pages = '<div class="ibox-content" id="pages-wrap">';
        if ($use_info)
        {
            //$pages .= "<span>共 {$total_page} 页，{$total_rs} 记录</span>\n";
            $pages .= "<select class=\"page_size\" onchange='pageFormSubmit();'>";
            $selected = $page_size == 10 ? "selected" : "";
            $pages .= "<option value='10' {$selected}>10</option>";
            $selected = $page_size == 20 ? "selected" : "";
            $pages .= "<option value='20' {$selected}>20</option>";
            $selected = $page_size == 50 ? "selected" : "";
            $pages .= "<option value='50' {$selected}>50</option>";
            $selected = $page_size == 100 ? "selected" : "";
            $pages .= "<option value='100' {$selected}>100</option>";
            $selected = $page_size == 200 ? "selected" : "";
            $pages .= "<option value='200' {$selected}>200</option>";
            $selected = $page_size == 500 ? "selected" : "";
            $pages .= "<option value='500' {$selected}>500</option>";
            $pages .= "</select> ";
            $pages .= "<span>共{$total_rs}条</span> \n";
        }
        $pages .= '';

        // 下一页
        $next_page = $current_page + 1;
        // 上一页
        $prev_page = $current_page - 1;
        // 末页
        $last_page = $total_page;

        // 上一页、首页
        if ($current_page > 1)
        {
            $pages .= "<a href='{$url_prefix}'>首页</a>\n";
            $pages .= "<a href='{$url_prefix}&{$page_name}={$prev_page}'>上一页</a>\n";
        }
        else
        {
            $pages .= "<a class='disabled' href='javascript:;'>上一页</a>\n";
        }

        // 前偏移
        for ($i = $current_page - $move_size; $i < $current_page; $i ++)
        {
            if ($i < 1)
            {
                continue;
            }
            $pages .= "<a href='{$url_prefix}&{$page_name}={$i}'>$i</a>\n";
        }
        // 当前页
        $pages .= "<a class='active' href='javascript:;'>" . $current_page . "</a>\n";

        // 后偏移
        $flag = 0;
        if ($current_page < $total_page)
        {
            for ($i = $current_page + 1; $i <= $total_page; $i ++)
            {
                $pages .= "<a href='{$url_prefix}&{$page_name}={$i}'>$i</a>\n";
                $flag ++;
                if ($flag == $move_size)
                {
                    break;
                }
            }
        }

        // 下一页、末页
        if ($current_page != $total_page)
        {
            $pages .= "<a href='{$url_prefix}&{$page_name}={$next_page}'>下一页</a>\n";
            $pages .= "<a href='{$url_prefix}&{$page_name}={$last_page}'>末页</a>\n";
        }
        else
        {
        }

        $pages .= '';

        // 输入框跳转
        if ($jump_info)
        {
            $pages .= " <input type=\"text\" class=\"page_no\" value=\"{$current_page}\" /><button type='button' class='btn' onclick=\"javascript:pageFormSubmit();\">Go</button>";
        }

        $pages .= '</div>';
        return array(
            'show'      => $pages,
            'page_size' => $page_size,
            'offset'    => $offset,
        );
    }
}
