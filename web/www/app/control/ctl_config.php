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
 * 配置管理
 *
 * @version $Id$
 */
class ctl_config
{
    public static $options = array(
        'config' => '基本配置',
        'attachment' => '附件设置',
        'doc' => '文档设置',
    );

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        tpl::assign( 'options', self::$options );
        tpl::assign( 'group', 'config' );
    }

    /**
     * 管理员帐号管理
     */
    public function index()
    {
        $keyword = req::item('keyword', '');
        $group = req::item('group', 'config');

        $where = array();
        if (!empty($keyword)) 
        {
            $where[] = array( 'name', 'like', "%{$keyword}%" );
        }
        if (!empty($group)) 
        {
            $where[] = array( 'group', '=', $group );
        }

        $row = db::select('count(*) AS `count`')
            ->from('#PB#_config')
            ->where($where)
            ->as_row()
            ->execute();

        $pages = cls_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_config')
            ->where($where)
            ->order_by('sort', 'asc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        tpl::assign('group', $group);
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('config.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')->from('#PB#_config')->where('name', $name)->as_row()->execute();
            if ( $row['count'] )
            {
                cls_msgbox::show('系统提示', '变量名称已经存在！', '-1');
                exit();
            }

            db::insert('#PB#_config')
                ->set([
                    'name'  => req::item('name'),
                    'group' => req::item('group'),
                    'title' => req::item('title'),
                    'value' => req::item('value'),
                    'sort'  => req::item('sort', 100),
                    'type'  => req::item('type'),
                ])
                ->execute();

            $this->_cache();
            kali::$auth->save_admin_log("配置添加 {$name}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "添加成功", $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('config.add.tpl');
        }
    }

    public function edit()
    {
        $name = req::item('name', '');
        if (!empty(req::$posts)) 
        {
            db::update('#PB#_config')
                ->set([
                    'name'  => req::item('name'),
                    'group' => req::item('group'),
                    'title' => req::item('title'),
                    'value' => req::item('value'),
                    'sort'  => req::item('sort'),
                    'type'  => req::item('type'),
                ])
                ->where('name', $name)
                ->execute();

             $this->_cache();
            kali::$auth->save_admin_log("配置修改 {$name}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_config')
                ->where('name', $name)
                ->as_row()
                ->execute();
            tpl::assign('v', $v);
            req::set_redirect( req::forword() );
            tpl::display('config.edit.tpl');
        }
    }

    public function del()
    {
        $ids = implode(',', req::item('ids', 0));
        db::delete('#PB#_config')
            ->where('sort', 'in', $ids)
            ->execute();

        $this->_cache();
        kali::$auth->save_admin_log("配置删除 {$ids}");
        cls_msgbox::show('系统提示', "删除成功", req::forword());
    }

    public function batch_edit()
    {
        $sorts = req::item('sorts', array());
        $datas = req::item('datas', array());
        foreach ($sorts as $name=>$sort) 
        {
            db::update('#PB#_config')->set(array(
                'sort' => intval(trim($sort))
            ))->where('name', $name)
            ->execute();
        }
        foreach ($datas as $name=>$value) 
        {
            db::update('#PB#_config')
                ->set([
                    'value' => trim($value)
                ])
                ->where('name', $name)
                ->execute();
        }

        //config::reload();
        $this->_cache();
        kali::$auth->save_admin_log("配置批量修改");
        cls_msgbox::show('系统提示', "批量修改成功", req::forword());
    }

    private function _cache()
    {
        return config::instance('db_config')->cache(null, true);
    }

}
