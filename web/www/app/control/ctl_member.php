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
use kaliphp\lib\cls_benchmark;

/**
 * 会员管理
 *
 * @version $Id$
 */
class ctl_member
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
        //cls_profiler::instance()->enable_profiler(true);

        $keyword = req::item('keyword', '');

        $where = array();
        if (!empty($keyword)) 
        {
            //$where[] = array( 'name', 'like', "%$keyword%" );
            //$where[] = array( 'address', 'like', "%$keyword%", 'OR' );
            $where[] = array( 'CONCAT(`name`, `address`)', 'like', "%$keyword%" );
        }

        cls_benchmark::mark("操作数据库1_start");

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_member')
            ->where($where)
            ->as_row()
            ->execute();
        
        cls_benchmark::mark("操作数据库1_end");

        $pages = cls_page::make($row['count'], 10);

        cls_benchmark::mark("操作数据库2_start");

        //$list = db::select(array("CONCAT(name, address) AS name2"))
        $list = db::select('id, name, email')
            ->from('#PB#_member')
            ->where($where)
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        cls_benchmark::mark("操作数据库2_end");
        // echo cls_benchmark::elapsed_time("操作数据库_start");
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('member.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_member')
                ->where('name', $name)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            list($insert_id, $rows_affected) = db::insert('#PB#_member')
                ->set([
                    'name'        => $name,
                    'age'         => req::item('age'),
                    'email'       => req::item('email'),
                    'address'     => req::item('address'),
                    'create_user' => kali::$auth->uid,
                    'create_time' => time(),
                ])
                ->execute();

            kali::$auth->save_admin_log("会员添加 {$insert_id}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', '添加成功', $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('member.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')->from('#PB#_member')
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            db::update('#PB#_member')->set(array(
                'name'        => $name,
                'age'         => req::item('age'),
                'email'       => req::item('email'),
                'address'     => req::item('address'),
                'update_user' => kali::$auth->uid,
                'update_time' => time(),
            ))
            ->where('id', $id)
            ->execute();

            kali::$auth->save_admin_log("会员修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', '修改成功', $gourl);
        }
        else 
        {
            $v = db::select('name, age, email, address')
                ->from('#PB#_member')
                ->where('id', $id)
                ->as_row()
                ->execute();
            req::set_redirect( req::forword() );
            tpl::assign('v', $v);
            tpl::display('member.edit.tpl');
        }
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的会员", -1);
        }

        db::delete('#PB#_member')->where('id', 'in', $ids)->execute();

        kali::$auth->save_admin_log("会员删除 ".implode(",", $ids));

        cls_msgbox::show('系统提示', '删除成功',  req::forword());
    }

}
