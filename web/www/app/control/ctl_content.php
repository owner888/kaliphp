<?php
namespace control;

use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\config;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_page;

/**
 * 内容管理
 *
 * @version $Id$
 */
class ctl_content
{
    public static $config = [];
    public static $options = array(0 => '请选择分类');

    public static function _init()
    {
        self::$config = config::instance('upload')->get();
    }

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $rows = db::select('id, name')->from('#PB#_category')->order_by('sort', 'asc')->execute();
        foreach ($rows as $row) 
        {
            self::$options[$row['id']] = $row['name'];
        }
        tpl::assign( 'options', self::$options );
        tpl::assign( 'catid', 0 );
    }

    public function index()
    {
        $keyword = req::item('keyword', '');
        $catid   = req::item('catid', 0);

        $where = array();
        if (!empty($keyword)) 
        {
            $where[] = array( 'name', 'like', "%$keyword%" );
        }
        if (!empty($catid)) 
        {
            $where[] = array( 'catid', '=', $catid );
        }

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_content')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = cls_page::make($row['count'], 10);

        $list = db::select()->from('#PB#_content')
            ->where($where)
            ->order_by('id', 'desc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        if (!empty($list)) 
        {
            foreach ($list as $k=>$v) 
            {
                $list[$k]['imageurl'] = self::$config['filelink']."/image/".$v['image'];
            }
        }

        tpl::assign('catid', $catid);
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('content.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_content')
                ->where('name', $name)
                ->as_row()
                ->execute();
            if ( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            list($insert_id, $rows_affected) = db::insert('#PB#_content')->set(array(
                'name'        => $name,
                'image'       => req::item('image'),
                'images'      => req::item('images'),
                'catid'       => req::item('catid'),
                'content'     => req::item('content'),
                'create_user' => kali::$auth->uid,
                'create_time' => time(),
            ))
            ->execute();

            kali::$auth->save_admin_log("内容添加 {$insert_id}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "添加成功", $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('content.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')->from('#PB#_content')
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->as_row()
                ->execute();
            if ( $row['count'] )
            {
                cls_msgbox::show('系统提示', '标题已经存在！', '-1');
                exit();
            }

            //echo '<pre>';print_r(array(
                //'name'        => $name,
                //'image'       => req::item('image'),
                //'images'      => req::item('images'),
                //'catid'       => req::item('catid'),
                //'content'     => req::item('content'),
                //'create_user' => kali::$auth->uid,
                //'create_time' => time(),
            //));echo '</pre>';
            //exit;

            db::update('#PB#_content')->set(array(
                'name'        => $name,
                'image'       => req::item('image'),
                'images'      => req::item('images'),
                'catid'       => req::item('catid'),
                'content'     => req::item('content'),
                'update_user' => kali::$auth->uid,
                'update_time' => time(),
            ))
            ->where('id', $id)
            ->execute();

            kali::$auth->save_admin_log("内容修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_content')
                ->where('id', $id)
                ->as_row()
                ->execute();
            
            $uploadlink = self::$config['filelink'];

            if (empty($v['image'])) 
            {
                $v['image'] = array(
                    'filename' => '',
                    'filelink' => 'static/img/addimage.png',
                );
            }
            else 
            {
                $v['image'] = array(
                    'filename' => $v['image'],
                    'filelink' => $uploadlink."/image/".$v['image'],
                );
            }

            if (empty($v['images'])) 
            {
                $v['images'] = array();
            }
            else 
            {
                $images = explode(",", $v['images']);
                $image_arr = array();
                foreach ($images as $image) 
                {
                    $image_arr[] = array(
                        'filename' => $image,
                        'filelink' => $uploadlink."/image/".$image,
                    );
                }
                $v['images'] = $image_arr;
            }

            tpl::assign('v', $v);
            req::set_redirect( req::forword() );
            tpl::display('content.edit.tpl');
        }
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的内容", $gourl);
        }

        db::delete('#PB#_content')
            ->where('id', 'in', $ids)
            ->execute();

        kali::$auth->save_admin_log("内容删除 ".implode(",", $ids));

        cls_msgbox::show('系统提示', "删除成功", req::forword());
    }

    public function info()
    {
        $id = req::item("id", 0);
        $info = db::select()
            ->from('#PB#_content')
            ->where('id', $id)
            ->as_row()
            ->execute();
        tpl::assign("info", $info);
        tpl::display('content.info.tpl');
    }

}
