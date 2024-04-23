<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\kali;
use kaliphp\util;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_page;
use kaliphp\lib\cls_menu;

/**
 * 权限组管理
 *
 * @version $Id$
 */
class ctl_admin_group
{
    public static $table = '#PB#_admin_group';

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
    }

    public function index()
    {
        $keyword = req::item('keyword', '');

        $where = array();
        if (!empty($keyword)) 
        {
            $where[] = array( 'name', 'like', "%$keyword%" );
        }

        $count = db::select_count(self::$table, $where);
        $pages = cls_page::make($count, 10);

        $list = db::select()
            ->from('#PB#_admin_group')
            ->where($where)
            ->order_by('addtime', 'ASC')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('admin_group.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_config')
                ->where('name', $name)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '用户组名已经存在！', '-1');
                exit();
            }

            $purviews = req::item('purviews');
            $purviews = empty($purviews) ? '' : implode(",", $purviews);
            $id = util::random('web');
            db::insert('#PB#_admin_group')->set(array(
                'id'        => $id,
                'name'      => $name,
                'purviews'  => $purviews,
                'addtime'   => time(),
                'uptime'    => time(),
            ))
            ->execute();

            kali::$auth->save_admin_log("用户组添加 {$id}");

            //cache::del('cls_auth_cfg_admin_group', 'admin');

            cls_msgbox::show('系统提示', "添加成功", req::redirect());
        }
        else 
        {
            $purviews = cls_menu::get_purviews(true, false);

            req::set_redirect( req::forword() );
            tpl::assign('purviews', $purviews);
            tpl::display('admin_group.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item('id', '');
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')->from('#PB#_admin_group')
                ->where('name', $name)
                ->and_where('id', '!=', $id)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '用户组名已经存在！', '-1');
                exit();
            }

            $purviews = req::item('purviews');
            $purviews = empty($purviews) ? '' : implode(",", $purviews);

            db::update('#PB#_admin_group')->set(array(
                'name'     => $name,
                'purviews' => $purviews,
                'uptime'   => time(),
            ))
            ->where('id', $id)
            ->execute();

            kali::$auth->save_admin_log("用户组修改 {$id}");

            //cache::del('cls_auth_cfg_admin_group', 'admin');

            cls_msgbox::show('系统提示', "修改成功", req::redirect());
        }
        else 
        {
            $info = db::select('name, purviews')
                ->from('#PB#_admin_group')
                ->where('id', $id)
                ->as_row()
                ->execute();

            $purviews = cls_menu::get_purviews(true, false);

            tpl::assign('purviews', $purviews);
            tpl::assign('info', $info);
            req::set_redirect( req::forword() );
            tpl::display('admin_group.edit.tpl');
        }
    }

    public function del()
    {
        $id = req::item('id', '');
        if ( !$id) 
        {
            cls_msgbox::show('系统提示', '请选择要删除的用户组！', '-1');
        }

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_admin')
            ->where('groups','find_in_set', $id)
            ->as_row()
            ->execute();
        if ($row['count']) 
        {
            cls_msgbox::show('系统提示', '用户组下面存在用户，不可删除！', '-1');
        }

        db::delete('#PB#_admin_group')
            ->where('id', $id)
            ->execute();

        kali::$auth->save_admin_log("用户组删除 {$id}");

        cls_msgbox::show('系统提示', "删除成功", req::forword());
    }

}
