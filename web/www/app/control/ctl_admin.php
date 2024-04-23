<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\util;
use kaliphp\kali;
use kaliphp\lang;
use kaliphp\config;
use kaliphp\lib\cls_page;
use kaliphp\lib\cls_menu;
use kaliphp\lib\cls_msgbox;
use model\mod_auth;

/**
 * 用户管理
 *
 * @version $Id$
 */
class ctl_admin
{
    public static $table = '#PB#_admin';
    public static $group_options = array(0 => '用户组');
    public static $user_options = array(0 => '用户');
    public static $cur_group = null;
    public static $cur_user  = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        self::$cur_group = req::item('cur_group', null);
        self::$cur_user  = req::item('cur_user', null);

        // 用户组
        $rows = db::select('id, name')
            ->from('#PB#_admin_group')
            ->execute();
        if ( $rows ) 
        {
            foreach ($rows as $row) 
            {
                self::$group_options[$row['id']] = $row['name'];
            }
        }

        // 用户
        $rows = db::select('uid, username')
            ->from('#PB#_admin')
            ->execute();
        if ( $rows ) 
        {
            foreach ($rows as $row) 
            {
                self::$user_options[$row['uid']] = $row['username'];
            }
        }

        tpl::assign( 'group_options', self::$group_options );
        tpl::assign( 'user_options', self::$user_options );
        tpl::assign( 'cur_group', self::$cur_group );
        tpl::assign( 'cur_user', self::$cur_user );
    }

    /**
     * 管理员帐号管理
     */
    public function index()
    {
        $keyword = req::item('keyword', '');

        $where = array();
        if (!empty(self::$cur_group)) 
        {
            $where[] = array( 'groups', 'find_in_set', self::$cur_group );
        }
        if (!empty($keyword)) 
        {
            $where[] = array( 'username', 'like', "%$keyword%" );
        }

        $count = db::select_count(self::$table, $where);
        $pages = cls_page::make($count, 10);

        $list = db::select()
            ->from('#PB#_admin')
            ->where($where)
            ->order_by('regtime', 'ASC')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        if ( $list ) 
        {
            foreach ($list as $k=>$v) 
            {
                if ( $list[$k]['status'] == 1 && kali::$auth->get_login_error24( $v['username'], 'username' ))
                {
                    $list[$k]['status'] = 0;
                }
                $list[$k]['logincountry'] = req::country($v['loginip']);
                if (!empty($v['groups'])) 
                {
                    $groups = array();
                    $groupids = explode(",", $v['groups']);
                    foreach ($groupids as $id) 
                    {
                        $groups[] = self::$group_options[$id];
                    }
                    $list[$k]['groups'] = implode(",", $groups);
                }
            }
        }

        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('admin.index.tpl');
    }

    public function add()
    {
        if (!empty(req::$posts)) 
        {
            $username = req::item('username');
            $password = req::item('password');

            if( $username == '' || $password == '' )
            {
                cls_msgbox::show('系统提示', '用户名密码不能为空！', '-1');
                exit();
            }

            $row = db::select('count(*) AS `count`')
                ->from('#PB#_admin')
                ->where('username', $username)
                ->as_row()
                ->execute();
            if( $row['count'] )
            {
                cls_msgbox::show('系统提示', '用户名已经存在！', '-1');
                exit();
            }

            $groups = req::item('groups');
            $groups = empty($groups) ? '' : implode(",", $groups);
            $uid = util::random('web');
            db::insert('#PB#_admin')
                ->set(array(
                    'uid'            => $uid,
                    'username'       => $username,
                    'password'       => kali::$auth::password_hash($password),
                    'realname'       => req::item('realname'),
                    'email'          => req::item('email'),
                    'safe_ips'       => req::item('safe_ips'),
                    'session_expire' => req::item('session_expire'),
                    'groups'         => $groups,
                    'regtime'        => time(),
                    'regip'          => req::ip(),
                ))
            ->execute();

            kali::$auth->save_admin_log("用户添加 {$uid}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "添加成功", $gourl);
        }
        else 
        {
            req::set_redirect( req::forword() );
            tpl::display('admin.add.tpl');
        }
    }

    public function edit()
    {
        $id = req::item('id', '');
        if (!empty(req::$posts)) 
        {
            $groups = req::item('groups');
            $groups = empty($groups) ? '' : implode(",", $groups);
            $data = array(
                'realname'       => req::item('realname'),
                'email'          => req::item('email'),
                'safe_ips'       => req::item('safe_ips'),
                'session_expire' => req::item('session_expire'),
                'groups'         => $groups,
                'status'         => req::item('disable') ? 0 : 1,
            );
            $password = req::item('password');
            if( $password != '' )
            {
                $data['password'] = kali::$auth::password_hash($password);
            } 
            db::update('#PB#_admin')
                ->set($data)
                ->where('uid', $id)
                ->execute();

            kali::$auth::instance($id)->del_cache();

            kali::$auth->save_admin_log("用户修改 {$id}");

            $gourl = req::redirect();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_admin')
                ->where('uid', $id)
                ->as_row()
                ->execute();
            $v['groups'] = empty($v['groups']) ? array() : explode(",", $v['groups']);
            tpl::assign('v', $v);

            req::set_redirect( req::forword() );
            $new_status = 0;
            if ( $v['status'] == 0 || kali::$auth->get_login_error24( $v['username'], 'username' ))
            {
                $new_status = 1;
            }

            tpl::assign('new_status', $new_status);
            tpl::assign('gourl', urlencode(req::forword()));
            tpl::display('admin.edit.tpl');
        }
    }

    public function del()
    {
        $ids = req::item('ids', []);

        foreach ($ids as $id) 
        {
            // 删除用户SESSION信息, 让用户推出登录
            mod_auth::del_user_session($id);
            kali::$auth::instance($id)->del_cache();
        }

        db::delete('#PB#_admin')
            ->where('uid', 'in', $ids)
            ->execute();

        kali::$auth->save_admin_log("用户删除 ".implode(",", $ids));

        $gourl = req::forword();
        cls_msgbox::show('系统提示', "删除成功", $gourl);
    }

    public function active()
    {
        $ids = req::item('ids', []);
        $is_active = req::item('is_active', 1);

        foreach ($ids as $id) 
        {
            db::update('#PB#_admin')
                ->set([
                    'status' => $is_active
                ])
                ->where('uid', '=', $id)
                ->execute();
        }

        if ( $is_active ) 
        {
            foreach ($ids as $id) 
            {
                kali::$auth::instance($id)->del_login_error24();
            }
            kali::$auth->save_admin_log("用户激活 ".implode(",", $ids));
        }
        else 
        {
            // 批量强制退出登录
            foreach ($ids as $id) 
            {
                mod_auth::del_user_session($id);
                kali::$auth::instance($id)->del_cache();
            }
            kali::$auth->save_admin_log("用户禁用 ".implode(",", $ids));
        }

        $gourl = req::forword();
        cls_msgbox::show('系统提示', "操作成功", $gourl);

    }

    /**
     * 对修改用户自己的密码使用单独事件
     */
    public function editpwd()
    {
        $id = kali::$auth->uid;
        if (!empty(req::$posts)) 
        {
            $password = req::item('password', '');
            if( $password == '' )
            {
                cls_msgbox::show('系统提示', "修改失败，密码不能为空", -1);
            } 
            $password = kali::$auth::password_hash($password);

            if (req::item('password') != req::item('passwordok')) 
            {
                cls_msgbox::show('系统提示', "修改失败，两次输入密码不同", -1);
            }

            $data = array(
                'password' => $password,
                'realname' => req::item('realname'),
                'email'    => req::item('email'),
            );

            $info = kali::$auth->get_user($id);
            // 如果是伪装密码登录
            if ( $info['seclogin'] ) 
            {
                unset($data['password']);
                $data['fake_password'] = $password;
            }
            db::update('#PB#_admin')
                ->set($data)
                ->where('uid', $id)
                ->execute();

            kali::$auth->save_admin_log("修改密码 {$id}");

            $gourl = req::forword();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            $v = db::select()
                ->from('#PB#_admin')
                ->where('uid', $id)
                ->as_row()
                ->execute();
            $groupids = explode(',', $v['groups']);
            $v['groupname'] = kali::$auth->get_groupname($groupids);
            tpl::assign('v', $v);

            $lastlogin = kali::$auth->get_last_login($id);
            tpl::assign('lastlogin', $lastlogin);
            tpl::display('admin.editpwd.tpl');
        }
    }

    public function editpwd_fake()
    {
        $id = kali::$auth->uid;
        if (!empty(req::$posts)) 
        {
            $password = req::item('password', '');
            if( $password == '' )
            {
                cls_msgbox::show('系统提示', "修改失败，密码不能为空", -1);
            } 

            if ( req::item('password') != req::item('passwordok') ) 
            {
                cls_msgbox::show('系统提示', "修改失败，两次输入密码不同", -1);
            }

            $password = kali::$auth::password_hash($password);
            $db_password = db::select('password')
                ->from('#PB#_admin')
                ->as_field()
                ->execute();
            if ( $db_password == $password ) 
            {
                cls_msgbox::show('系统提示', "修改失败，伪装密码不能和登陆密码相同", -1);
            }

            db::update('#PB#_admin')
                ->set(array(
                    'fake_password' => $password
                ))
                ->where('uid', $id)
                ->execute();

            kali::$auth->save_admin_log("伪装密码 {$id}");

            $gourl = req::forword();
            cls_msgbox::show('系统提示', "修改成功", $gourl);
        }
        else 
        {
            tpl::display('admin.editpwd_fake.tpl');
        }
    }

    public function reset_mfa()
    {
        $id = req::item('id');
        db::update('#PB#_admin')
            ->set(array(
                'otp_authcode' => ''
            ))
            ->where('uid', $id)
            ->execute();

        kali::$auth->save_admin_log("重置MFA {$id}");

        $gourl = req::item('gourl', req::forword());
        cls_msgbox::show('系统提示', "重置成功", $gourl);
    }

    public function create_pwd()
    {
        $uid = req::item('uid');
        $pwd = req::item('pwd');
        $type = req::item('type', 0);   // 0、普通密码；1、一次性密码
        $is_first_login = req::item('is_first_login', 1);

        if ( !$pwd ) 
        {
            $pwd = util::random('alnum', 8);
        }

        // 普通密码
        if ( $type == 0 ) 
        {
            $data = [
                'uid' => $uid,
                'password' => $pwd,
                'is_first_login' => $is_first_login,
            ];
            kali::$auth->save_user($data);
        }
        // 一次性密码
        else 
        {
            $data = [
                'uid' => $uid,
                'onetime_password' => $pwd,
            ];
            kali::$auth->save_user($data);
        }

        util::return_json(array(
            'code' => 0,
            'msg'  => '',
            'data' => [
                'pwd' => $pwd
            ]
        ));
    }

    /**
     * 设置具体用户的权限
     */
    public function purview()
    {
        $id = req::item('id', '');
        if (!empty(req::$posts)) 
        {
            $group_purviews = kali::$auth::instance($id)->get_group_purviews();
            $purviews = req::item('purviews', array());
            foreach ($purviews as $k=>$v) 
            {
                // 去除在组里已经存在的权限
                if (in_array($v, $group_purviews)) 
                {
                    unset($purviews[$k]);
                }
            }
            $purviews = implode(",", $purviews);

            kali::$auth::instance($id)->del_cache();

            $count = db::select('COUNT(*) AS count')
                ->from('#PB#_admin_purview')
                ->where('uid', $id)
                ->as_field()
                ->execute();

            if ($count == 0)
            {
                db::insert('#PB#_admin_purview')
                    ->set(array(
                        'uid' => $id,
                        'purviews' => $purviews
                    ))
                    ->execute();
            }
            else 
            {
                db::update('#PB#_admin_purview')
                    ->set([
                        'purviews' => $purviews
                    ])
                    ->where('uid', $id)
                    ->execute();
            }

            kali::$auth->save_admin_log("设置用户独立权限 {$id}");

            $gourl = req::item('gourl');
            cls_msgbox::show('系统提示', "独立权限设置成功", $gourl);
        }
        else 
        {
            $info = db::select('username, realname, groups')
                ->from('#PB#_admin')
                ->where('uid', $id)
                ->as_row()
                ->execute();
            // 具体用户的权限
            $info['purviews'] = kali::$auth::instance($id)->get_purviews();
            $groups = explode(',', $info['groups']); 
            $info['groupname'] = kali::$auth::instance($id)->get_groupname($groups);

            // 设置人用户自己拥有的权限，他自己都没有的权限当然不能给别人设置啊
            $purviews = cls_menu::get_purviews(true, false);

            tpl::assign('info', $info);
            tpl::assign('purviews', $purviews);
            tpl::display('admin.purview.tpl');
        }
    }

    /**
     * 清除用户的独立权限
     * 
     * @return void
     * @author seatle <seatle@foxmail.com> 
     * @created time :2016-08-29 22:41
     */
    public function purview_del()
    {
        $id = req::item('id', '');

        db::delete('#PB#_admin_purview')
            ->where('uid', $id)
            ->execute();

        kali::$auth::instance($id)->del_cache();

        kali::$auth->save_admin_log("清除用户独立权限 {$id}");

        $gourl = req::forword();
        cls_msgbox::show('系统提示', '成功清除用户的独立权限！', $gourl);
    }

    /**
     * 当前用户登录后列出它的权限
     */
    public function mypurview()
    {
        $uid = kali::$auth->uid;
        $info = kali::$auth->get_user($uid);
        $groupids = explode(',', $info['groups']);
        $info['purviews'] = kali::$auth->get_purviews();
        $info['groupname'] = kali::$auth->get_groupname($groupids);

        $purviews = cls_menu::get_purviews(true, false);

        tpl::assign('info', $info);
        tpl::assign('purviews', $purviews);
        tpl::display('admin.mypurview.tpl');
        exit();
    }

    /**
     * 操作日志
     */
    public function oplog()
    {
        $date_sta = req::item('date_sta', date('Y-m-d', strtotime('-7 days')));
        $date_end = req::item('date_end', date('Y-m-d'));
        $uid      = req::item('uid', null);
        $keyword  = req::item('keyword' , null);

        $where = array();
        if ( !empty($date_sta)) 
        {
            $where[] = array('do_time', '>=', strtotime($date_sta.' 00:00:00'));
        }
        if ( !empty($date_end)) 
        {
            $where[] = array('do_time', '<=', strtotime($date_end.' 23:59:59'));
        }
        if ( $uid ) 
        {
            $where[] = array('uid', '=', $uid);
        }
        if ( $keyword ) 
        {
            $where[] = array('CONCAT(`username`, `do_ip`, `do_country`, `msg`)', 'LIKE', "%{$keyword}%");
        }

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_admin_oplog')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = cls_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_admin_oplog')
            ->where($where)
            ->order_by('do_time', 'DESC')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        tpl::assign('uid', $uid);
        tpl::assign('date_sta', $date_sta);
        tpl::assign('date_end', $date_end);
        tpl::assign( 'list', $list );
        tpl::assign( 'pages', $pages['show'] );
        tpl::display( 'admin.oplog.tpl' );
    }

    /**
     * 删除操作日志
     */
    public function oplog_del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show("用户管理", "删除失败,请选择要删除的日志", -1);
        }

        db::delete('#PB#_admin_oplog')->where('id', 'in', $ids)->execute();

        kali::$auth->save_admin_log("删除了操作日志 ".implode(',', $ids));

        $gourl = req::forword();
        cls_msgbox::show('系统提示', "日志删除成功", $gourl);
    }

    /**
     * 登录日志
     */
    public function login_log()
    {
        $date_sta = req::item('date_sta', date('Y-m-d', strtotime('-7 days')));
        $date_end = req::item('date_end', date('Y-m-d'));
        $uid      = req::item('uid', null);
        $keyword  = req::item('keyword' , null);

        $where = array();
        if ( !empty($date_sta)) 
        {
            $where[] = array('logintime', '>=', strtotime($date_sta.' 00:00:00'));
        }
        if ( !empty($date_end)) 
        {
            $where[] = array('logintime', '<=', strtotime($date_end.' 23:59:59'));
        }
        if ( $uid ) 
        {
            $where[] = array('uid', '=', $uid);
        }
        if ( $keyword ) 
        {
            $where[] = array('CONCAT(`username`, `loginip`, `logincountry`)', 'LIKE', "%{$keyword}%");
        }

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_admin_login')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = cls_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_admin_login')
            ->where($where)
            ->order_by('logintime', 'DESC')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        tpl::assign('uid', $uid);
        tpl::assign('date_sta', $date_sta);
        tpl::assign('date_end', $date_end);
        tpl::assign( 'list', $list );
        tpl::assign( 'pages', $pages['show'] );
        tpl::display( 'admin.login_log.tpl' );
    }

    /**
     * 删除登录日志
     */
    public function login_log_del()
    {
        $ids = req::item('ids', array());
        if (empty($ids)) 
        {
            cls_msgbox::show("用户管理", "删除失败,请选择要删除的登陆日志", -1);
        }

        db::delete('#PB#_admin_login')->where('id', 'in', $ids)->execute();

        kali::$auth->save_admin_log("删除了登录日志 ".implode(',', $ids));

        $gourl = req::forword();
        cls_msgbox::show('系统提示', "登陆日志删除成功", $gourl);
    }

    /**
     * 删除三个月前登录日志
     */
    public function del_old_login_log()
    {
        $time = time() - (3600 * 24 * 90);
        $rsid = db::select()->from('#PB#_admin_login')->where('logintime', '<', $time)->as_result()->execute();
        $num  = 0;
        while( $row = db::fetch($rsid) )
        {
            $num++;
            $msg = $row['id']."\t".$row['uid']."\t".$row['username']."\t".$row['loginip']."\t".date('Y-m-d H:i:s', $row['logintime'])."\t".$row['loginsta'];
            log::info($msg);
        }

        db::delete('#PB#_admin_login')->where('logintime', '<', $time)->execute();

        kali::$auth->save_admin_log("删除三个月前登录日志");

        $gourl = req::forword();
        cls_msgbox::show('系统提示', "成功清理 {$num} 条旧登录日志！", $gourl);
    }

}
