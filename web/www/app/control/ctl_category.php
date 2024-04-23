<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\lang;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_page;

/**
 * 分类管理
 *
 * @version $Id$
 */
class ctl_category
{
    public function index()
    {
        $keyword = req::item('keyword', '');

        $where = array();
        if (!empty($keyword)) 
        {
            $where[] = array( 'name', 'like', "%$keyword%" );
        }

        //pub_benchmark::mark("操作数据库");

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_category')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = cls_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_category')
            ->where($where)
            ->order_by('sort', 'asc')
            ->order_by('id', 'asc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('category.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_category')
                ->where('name', $name)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            list($insert_id, $rows_affected) = db::insert('#PB#_category')
                ->set(array(
                    'name'       => $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ))
                ->execute();

            kali::$auth->save_admin_log("分类添加 {$insert_id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_add', '添加成功'), $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('category.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_category')
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            db::update('#PB#_category')
                ->set(array(
                    'name'       => $name,
                    'updated_at' => date('Y-m-d H:i:s'),
                ))
                ->where('id', $id)
                ->execute();

            kali::$auth->save_admin_log("分类修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_category')
                ->where('id', $id)
                ->as_row()
                ->execute();

            req::set_redirect( req::forword() );

            tpl::assign('v', $v);
            tpl::display('category.edit.tpl');
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
            db::update('#PB#_category')
                ->set(array(
                    'sort' => $sort
                ))
                ->where('id', $id)
                ->execute();
        }

        kali::$auth->save_admin_log("分类批量修改 ".implode(",", $ids));

        $gourl = req::forword();
        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), $gourl);
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的分类", -1);
        }

        db::delete('#PB#_category')
            ->where('id', 'in', $ids)
            ->execute();

        kali::$auth->save_admin_log("分类删除 ".implode(",", $ids));

        $gourl = req::forword();
        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_delete', '删除成功'), $gourl);
    }

}
