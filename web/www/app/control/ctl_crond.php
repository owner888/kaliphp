<?php
namespace control;

use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;

/**
 * 计划任务管理控制器
 *
 * @version $Id$
 */
class ctl_crond
{
    public static $config = [];

    public static function _init()
    {
        self::$config = config::instance('crond')->get();
    }

    public function __construct()
    {
        tpl::assign('ns', time());
    }

    /**
     * 主入口
     */
    public function index()
    {
        $list = db::select('*')
            ->from('#PB#_crond')
            ->order_by('sort', 'asc') 
            ->limit(1000) 
            ->execute();

        tpl::assign('list', $list);
        tpl::display('crond.index.old.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name     = req::item('name');
            $filename = req::item('filename');
            if (empty($filename)) 
            {
                cls_msgbox::show('系统提示', '脚本文件名不能为空', '-1');
                exit();
            }

            db::insert('#PB#_crond')
                ->set([
                    'name'           => $name,
                    'filename'       => $filename,
                    'runtime_format' => req::item('runtime_format'),
                    'status'         => req::item('status'),
                    'creator_id'     => kali::$auth->uid,
                    'created_at'     => date('Y-m-d H:i:s'),
                ])
                ->execute();

            kali::$auth->save_admin_log("添加计划任务 {$name}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "添加成功", $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('crond.add.tpl');
        }
    }

    public function edit()
    {

        $id = req::item('id', 0);
        if (!empty(req::$posts)) 
        {
            $name     = req::item('name');
            $filename = req::item('filename');
            if (empty($filename)) 
            {
                cls_msgbox::show('系统提示', '脚本文件名不能为空', '-1');
                exit();
            }

            db::update('#PB#_crond')
                ->set([
                    'name'           => $name,
                    'filename'       => $filename,
                    'runtime_format' => req::item('runtime_format'),
                    'status'         => req::item('status'),
                    'updator_id'     => kali::$auth->uid,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ])
                ->where('id', $id)
                ->execute();

            kali::$auth->save_admin_log("修改计划任务 {$name}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_crond')
                ->where('id', $id)
                ->as_row()
                ->execute();
            tpl::assign('v', $v);
            req::set_redirect( req::forword() );
            tpl::display('crond.edit.tpl');
        }


    }
    public function del()
    {

        $ids = req::item('ids', 0);
        if (!is_array($ids)) 
        {
            $ids = implode(',', $ids);
        }

        db::delete('#PB#_crond')
            ->where('id', 'in', $ids)
            ->execute();

        kali::$auth->save_admin_log("删除计划任务 ". json_encode($ids));
        cls_msgbox::show('系统提示', "删除成功", req::forword());
    }
    public function batch_edit()
    {
        $sorts = req::item('sorts', array());
        $ids = [];
        foreach ($sorts as $id => $sort) 
        {
            db::update('#PB#_crond')->set(array(
                'sort'       => intval(trim($sort)),
                'updator_id' => kali::$auth->uid,
                'updated_at' => date('Y-m-d H:i:s'),
            ))->where('id', $id)
            ->execute();

            $ids[] = $id;
        }

        kali::$auth->save_admin_log("计划任务批量修改". json_encode($ids));
        cls_msgbox::show('系统提示', "批量修改成功", req::forword());

    }

    public function status()
    {
        $ids    = req::item('ids', []);
        $status = req::item('status', 0);
        if (!is_array($ids)) 
        {
            $ids = implode(',', $ids);
        }


        db::update('#PB#_crond')->set(array(
            'status' => intval($status),
            'updator_id' => kali::$auth->uid,
            'updated_at' => date('Y-m-d H:i:s'),
        ))->where('id', 'in', $ids)
        ->execute();

        kali::$auth->save_admin_log("计划任务修改状态" . json_encode($ids));
        cls_msgbox::show('系统提示', "修改成功", req::forword());

    }

}
