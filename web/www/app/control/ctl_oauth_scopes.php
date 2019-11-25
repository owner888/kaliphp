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
 * 授权管理
 *
 * @version $Id$
 */
class ctl_oauth_scopes
{
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

        //pub_benchmark::mark("操作数据库");

        $row = db::select('count(*) AS `count`')
            ->from('#PB#_oauth_scopes')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = pub_page::make($row['count'], 10);

        $list = db::select()->from('#PB#_oauth_scopes')
            ->where($where)
            ->order_by('id', 'asc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        //echo pub_benchmark::elapsed_time("操作数据库");

        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('oauth_scopes.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $scope = req::item('scope');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_oauth_scopes')
                ->where('scope', $scope)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '授权已经存在！', '-1');
                exit();
            }

            list($insert_id, $rows_affected) = db::insert('#PB#_oauth_scopes')->set(array(
                'name'        => req::item('name'),
                'scope'       => $scope,
                'desc'        => req::item('desc'),
                'is_default'  => req::item('is_default'),
                'create_user' => kali::$auth->uid,
                'create_time' => time(),
            ))
            ->execute();

            kali::$auth::save_admin_log("授权添加 {$insert_id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_add', '添加成功'), $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('oauth_scopes.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $scope = req::item('scope');
            $row = db::select('count(*) AS `count`')->from('#PB#_oauth_scopes')
                ->where('scope', $scope)
                ->where('id', '!=', $id)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '授权已经存在！', '-1');
                exit();
            }

            db::update('#PB#_oauth_scopes')->set(array(
                'name'        => req::item('name'),
                'scope'       => $scope,
                'desc'        => req::item('desc'),
                'is_default'  => req::item('is_default'),
                'update_user' => kali::$auth->uid,
                'update_time' => time(),
            ))
            ->where('id', $id)
            ->execute();

            kali::$auth::save_admin_log("授权修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), $gourl);
        }
        else 
        {
            $v = db::select()->from('#PB#_oauth_scopes')->where('id', $id)->as_row()->execute();
            tpl::assign('v', $v);
            req::set_redirect( req::forword() );
            tpl::display('oauth_scopes.edit.tpl');
        }
    }

    public function edit_batch()
    {
        $ids = req::item('ids', array());
        $sorts = req::item('sorts', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "未选中任何数据", -1);
        }
        foreach ($ids as $id) 
        {
            $sort = $sorts[$id];
            db::update('#PB#_oauth_scopes')->set(array(
                'sort' => $sort
            ))
            ->where('id', $id)->execute();
        }

        kali::$auth::save_admin_log("授权批量修改 ".implode(",", $ids));

        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), req::forword());
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的授权", $gourl);
        }

        db::delete('#PB#_oauth_scopes')->where('id', 'in', $ids)->execute();

        kali::$auth::save_admin_log("授权删除 ".implode(",", $ids));

        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_delete', '删除成功'), req::forword());
    }

}
