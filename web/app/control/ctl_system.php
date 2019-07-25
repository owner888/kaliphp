<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_page;


/**
 * 系统管理
 *
 * @version $Id$
 */
class ctl_system
{

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 后台管理菜单
     */
    public function edit_menu()
    {
        //"APP声明项"用于声明控制器，会在组权限管理的地方显示控制器名字。
        $this->_edit_hidden_config('admin_menu', '菜单配置', '后台菜单配置', '成功修改菜单配置', 'edit_menu', 450);
    }

    /**
     *  单项配置修改
     */
    private function _edit_hidden_config($key, $dotitle, $info, $alert_msg, $ac, $area_height=350)
    {
        tpl::assign('dotitle', $dotitle);
        tpl::assign('info', $info);
        tpl::assign('c_ac', $ac);
        tpl::assign('area_height', $area_height);

        if( req::method() == 'POST' )
        {
            $value = req::item('new_value');
            $value = htmlspecialchars_decode($value);
            config::save($key, $value);
            kali::$auth->save_admin_log( "修改了系统配置的 {$key} 项目的值");
            cls_msgbox::show('系统提示', $alert_msg, '?ct=system&ac='.$ac);
        }
        else
        {
            $value = config::get( $key );
            tpl::assign('value', $value);
            tpl::display( 'system.edit_hidden_config.tpl' );
        }
    }

}
