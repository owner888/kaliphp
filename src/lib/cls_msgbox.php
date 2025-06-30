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
use kaliphp\tpl;
use kaliphp\lang;

/**
 * 简单对话框类
 *
 * @version $Id$
 */
class cls_msgbox
{
    /**
    * 显示一个简单的对话框
    *
    * @param $title 标题
    * @param $msg 消息
    * @param $gourl 跳转网址（其中 javascript:; 或 空 表示不跳转）
    * @param $limittime 跳转时间
    *
    * @return void
    *
    */
    public static function show( $title, $msg, $gourl='', $limittime=3000 )
    {
        $title = $title == '' ? '系统提示信息' : $title;
        $jumpmsg = $jstmp = '';
        if ( $gourl=='javascript:;' )
        {
            $gourl == '';
        }
        //返回上一页
        if ( $gourl == -1 )
        {
           $gourl = "javascript:history.go(-1);";
        }
        if ( $gourl == -2 )
        {
            $jumpmsg = "<a href='?ac=logout'>重新登录</a>";
        }
        elseif ( $gourl != '' )
        {
            $browser_jump_hint = lang::get("common_browser_jump_hint", "如果你的浏览器没有自动跳转，请点击这里")."...";
            $jumpmsg = "<a href='{$gourl}'>{$browser_jump_hint}</a>";
            $jstmp = "setTimeout(\"JumpUrl('{$gourl}')\", {$limittime});";
        }

        tpl::assign('title', $title);
        tpl::assign('msg', $msg);
        tpl::assign('jumpmsg', $jumpmsg);
        tpl::assign('jstmp', $jstmp);

        $tpl = 'msgbox.show.tpl';
        if ( !tpl::exists($tpl))    
        {
            $tpl = 'system/'.$tpl;
        }
        tpl::display($tpl);
        exit();
    }

    public static function error( $http_code = '404' )
    {
        if ( $http_code == '404' ) 
        {
            header("HTTP/1.1 404 Not Found"); 
        }
        elseif ( $http_code == '500' ) 
        {
            header('HTTP/1.1 500 Internal Server Error');
        }

        $tpl = "msgbox.{$http_code}.tpl";
        // 如果当前项目模版目录不存在模板文件，读取system目录的
        if ( !tpl::exists($tpl))    
        {
            $tpl = 'system/'.$tpl;
        }
        tpl::display($tpl);
        exit();
    }
}
