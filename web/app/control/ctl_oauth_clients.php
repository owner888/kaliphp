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
 * 应用管理
 *
 * @version $Id$
 */
class ctl_oauth_clients
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
            ->from('#PB#_oauth_clients')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = pub_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_oauth_clients')
            ->where($where)
            ->order_by('id', 'asc')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        //echo pub_benchmark::elapsed_time("操作数据库");

        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('oauth_clients.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_oauth_clients')
                ->where('name', $name)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '应用已经存在！', '-1');
                exit();
            }

            $grant_types = req::item('grant_types');
            $grant_types = implode(",", $grant_types);
            $scope = req::item('scope');
            $scope = implode(",", $scope);
            list($insert_id, $rows_affected) = db::insert('#PB#_oauth_clients')
                ->set([
                    'name'          => req::item('name'),
                    'website'       => req::item('website'),
                    'desc'          => req::item('desc'),
                    'domain'        => req::item('domain'),
                    'ip'            => req::item('ip'),
                    'redirect_uri'  => req::item('redirect_uri'),
                    'cancel_uri'    => req::item('cancel_uri'),
                    'grant_types'   => $grant_types,
                    'scope'         => $scope,
                    'client_id'     => req::item('client_id'),
                    'client_secret' => req::item('client_secret'),
                    'create_user'   => kali::$auth->uid,
                    'create_time'   => time(),
                ])
                ->execute();

            kali::$auth->save_admin_log("应用添加 {$insert_id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_add', '添加成功'), $gourl);
        }
        else 
        {
            $scopes = db::select('scope, name, is_default')
                ->from('#PB#_oauth_scopes')
                ->order_by('id', 'asc')
                ->execute();
            tpl::assign('scopes', $scopes);

            $client_id = util::random('unique');
            $client_secret = util::random('unique');
            tpl::assign('client_id', $client_id);
            tpl::assign('client_secret', $client_secret);
            req::set_redirect( req::forword() );
            tpl::display('oauth_clients.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item("id", 0);
        if (!empty(req::$posts)) 
        {
            $name = req::item('name');
            $row = db::select('count(*) AS `count`')
                ->from('#PB#_oauth_clients')
                ->where('name', $name)
                ->where('id', '!=', $id)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '应用已经存在！', '-1');
                exit();
            }

            $grant_types = req::item('grant_types');
            $grant_types = implode(",", $grant_types);
            $scope = req::item('scope');
            $scope = implode(",", $scope);
            db::update('#PB#_oauth_clients')
                ->set([
                    'name'          => req::item('name'),
                    'website'       => req::item('website'),
                    'desc'          => req::item('desc'),
                    'domain'        => req::item('domain'),
                    'ip'            => req::item('ip'),
                    'redirect_uri'  => req::item('redirect_uri'),
                    'cancel_uri'    => req::item('cancel_uri'),
                    'grant_types'   => $grant_types,
                    'scope'         => $scope,
                    'client_id'     => req::item('client_id'),
                    'client_secret' => req::item('client_secret'),
                    'update_user'   => kali::$auth->uid,
                    'update_time'   => time(),
                ])
                ->where('id', $id)
                ->execute();

            kali::$auth->save_admin_log("应用修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), $gourl);
        }
        else 
        {
            $scopes = db::select('scope, name')
                ->from('#PB#_oauth_scopes')
                ->order_by('id', 'asc')
                ->execute();
            tpl::assign('scopes', $scopes);

            $v = db::select()
                ->from('#PB#_oauth_clients')
                ->where('id', $id)
                ->as_row()
                ->execute();
            $v['scope'] = explode(",", $v['scope']);
            $v['grant_types'] = explode(",", $v['grant_types']);
            tpl::assign('v', $v);
            req::set_redirect( req::forword() );
            tpl::display('oauth_clients.edit.tpl');
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
            db::update('#PB#_oauth_clients')->set(array(
                'sort' => $sort
            ))
            ->where('id', $id)->execute();
        }

        kali::$auth->save_admin_log("应用批量修改 ".implode(",", $ids));

        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_edit', '修改成功'), req::forword());
    }

    public function del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show('系统提示', "删除失败，请选择要删除的应用", $gourl);
        }

        db::delete('#PB#_oauth_clients')->where('id', 'in', $ids)->execute();

        kali::$auth->save_admin_log("应用删除 ".implode(",", $ids));

        cls_msgbox::show(lang::get('common_system_hint', '系统提示'), lang::get('common_success_delete', '删除成功'), req::forword());
    }

    public function ajax_get_client_secret()
    {
        $data = util::random('unique');
        util::return_json(array(
            'code' => 0,
            'msg'  => 'successful',
            'data' => $data,
        ));
    }
}
