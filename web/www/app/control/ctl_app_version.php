<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\kali;
use kaliphp\lib\cls_page;
use kaliphp\lib\cls_msgbox;
use common\extend\pub_app_version;

class ctl_app_version
{
    public static $log_prefix = 'APP Version';
    public static $table      = '#PB#_app_version';
    public static $os_options = [
        'Android' => 'Android',
        'iOS'     => 'iOS',
    ];

    public static function _init()
    {
    }

    public function __construct()
    {
        $condition_maps = pub_app_version::$condition_maps;
        $update_mode    = pub_app_version::$update_mode;
        $condition_mode_maps = pub_app_version::$condition_mode_maps;
        tpl::assign('condition_maps', $condition_maps);
        tpl::assign('update_mode', $update_mode);
        tpl::assign('condition_mode_maps', $condition_mode_maps);
        tpl::assign('os_options', self::$os_options);
    }

    public function index()
    {
        $keyword = req::item('keyword', '');
        $os = req::item('os', '');

        $where = array();
        if (!empty($keyword)) 
        {
            $where[] = ['name', 'like', "%$keyword%"];
        }
        if (!empty($os)) 
        {
            $where[] = ['os', '=', $os];
        }

        $count = db::select_count(self::$table, $where);
        $pages = cls_page::make($count, 10);
        $list = db::select()->from(self::$table)
            ->where($where)
            ->order_by('id', 'desc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        // if (!empty($list))
        // {
        //     foreach ($list as $k=>$v)
        //     {
        //     }
        // }

        tpl::assign('os_options', array_merge([0 => '所有'], self::$os_options));
        tpl::assign('os', $os);
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('app_version.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $data = [
                'os'       => req::item('os'),
                'bound_id' => req::item('bound_id'),
                'version'  => req::item('version'),
                'md5'      => req::item('md5'),
                'app_url'  => req::item('app_url'),
                'rules'    => req::item('rules'),
                'tips'     => req::item('tips'),
            ];
            $insert_id = pub_app_version::save($data);

            kali::$auth->save_admin_log(self::$log_prefix . "添加 {$insert_id}");

            cls_msgbox::show('系统提示', "添加成功", req::redirect());
        }
        else 
        {
            $data['os'] = 'Android';
            tpl::assign('version', $version ?? []);
            tpl::assign('data', $data);
            req::set_redirect(req::forword());
            tpl::display('app_version.form.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $data = [
                'id'       => $id,
                'os'       => req::item('os'),
                'bound_id' => req::item('bound_id'),
                'version'  => req::item('version'),
                'md5'      => req::item('md5'),
                'app_url'  => req::item('app_url'),
                'rules'    => req::item('rules'),
                'tips'     => req::item('tips'),
            ];
            pub_app_version::save($data);

            kali::$auth->save_admin_log(self::$log_prefix . "修改 {$id}");

            cls_msgbox::show('系统提示', "修改成功", req::redirect());
        }
        else 
        {
            $data = db::select()
                ->from(self::$table)
                ->where('id', $id)
                ->as_row()
                ->execute();
            $data = pub_app_version::format_data($data);
            tpl::assign('data', $data);
            req::set_redirect(req::forword());
            tpl::display('app_version.form.tpl');
        }
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的" . self::$log_prefix, req::forword());
        }

        db::delete(self::$table)->where('id', 'in', $ids)->execute();

        kali::$auth->save_admin_log(self::$log_prefix . "删除 ".implode(",", $ids));

        cls_msgbox::show('系统提示', "删除成功", req::forword());
    }

    public function info()
    {
        $id = req::item("id", 0);
        $info = db::select()
            ->from(self::$table)
            ->where('id', $id)
            ->as_row()
            ->execute();
        tpl::assign("info", $info);
        tpl::display('app_version.info.tpl');
    }
}
