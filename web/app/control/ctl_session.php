<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\util;
use kaliphp\config;
use kaliphp\session;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_page;
use model\mod_session;


/**
 * SESSION 管理
 *
 * @version $Id$
 */
class ctl_session
{
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
     * 登陆审核
     * 
     * @return void
     */
    public function verify()
    {
    
    }

    /**
     * 在线会话
     * 
     * @return void
     */
    public function online()
    {
        $this->_del_expired_session();

        $date_sta = req::item('date_sta', date('Y-m-d', strtotime('-7 days')));
        $date_end = req::item('date_end', date('Y-m-d'));
        $uid      = req::item('uid', null);
        $keyword  = req::item('keyword' , null);

        $where = array();
        $where[] = array( 'session_id', '!=', '' );
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
            $where[] = array('CONCAT(`username`, `loginip`)', 'LIKE', "%{$keyword}%");
        }

        $row = db::select('COUNT(*) AS `count`')
            ->from('#PB#_admin')
            ->where($where)
            ->as_row()
            ->execute();
        
        $pages = cls_page::make($row['count'], 10);

        $list = db::select()
            ->from('#PB#_admin')
            ->where($where)
            ->order_by('logintime', 'DESC')
            ->limit($pages['page_size'])
            ->offset($pages['offset'])
            ->execute();

        if ( $list) 
        {
            foreach ($list as $k=>$v) 
            {
                $list[$k]['expires'] = util::second2time(session::ttl($v['session_id']));
                $list[$k]['logincountry'] = req::country($v['loginip']);

                $actions = db::select('COUNT(*) AS `count`')
                    ->from('#PB#_admin_oplog')
                    ->where('session_id', $v['session_id'])
                    ->as_field()
                    ->execute();
                $list[$k]['actions'] = $actions;
                $list[$k]['terminate'] = true;
                if ($v['session_id'] == session_id()) 
                {
                    $list[$k]['terminate'] = false;
                }
            }
        }

        tpl::assign('uid', $uid);
        tpl::assign('date_sta', $date_sta);
        tpl::assign('date_end', $date_end);
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('session.online.tpl');
    }

    /**
     * 历史会话
     * 
     * @return void
     */
    public function offline()
    {
        $this->_del_expired_session();

        $date_sta = req::item('date_sta', date('Y-m-d', strtotime('-7 days')));
        $date_end = req::item('date_end', date('Y-m-d'));
        $uid      = req::item('uid', null);
        $keyword  = req::item('keyword' , null);

        $where = array();
        $where[] = array( 'session_id', '!=', '' );
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

        if ( $list) 
        {
            foreach ($list as $k=>$v) 
            {
                $list[$k]['expires'] = util::second2time(session::ttl($v['session_id']));

                $actions = db::select('COUNT(*) AS `count`')
                    ->from('#PB#_admin_oplog')
                    ->where('session_id', $v['session_id'])
                    ->as_field()
                    ->execute();
                $list[$k]['actions'] = $actions;
                $list[$k]['terminate'] = true;
                if ($v['session_id'] == session_id()) 
                {
                    $list[$k]['terminate'] = false;
                }
            }
        }

        tpl::assign('uid', $uid);
        tpl::assign('date_sta', $date_sta);
        tpl::assign('date_end', $date_end);
        tpl::assign('list', $list);
        tpl::assign('pages', $pages['show']);
        tpl::display('session.offline.tpl');
    }

    public function _del_expired_session()
    {
        $rows = db::select('session_id')
            ->from('#PB#_admin')
            ->where('session_id', '!=', '')
            ->execute();

        if ( $rows ) 
        {
            foreach ($rows as $row) 
            {
                $session_id = $row['session_id'];
                // 如果session已经过期，清空一下数据库数据
                if ( session::ttl($session_id) <= 0 )
                {
                    // 删除session_id值
                    db::update('#PB#_admin')
                        ->set([
                            'session_id' => ''
                        ])
                        ->where('session_id', $session_id)
                        ->execute();
                }    
            }
        }
    }

    /**
     * 终断登陆
     * 
     * @return void
     */
    public function terminate()
    {
        $uids = req::item('uids', array());
        if ( empty($uids)) 
        {
            cls_msgbox::show('系统提示', "终断失败，请选择要终断的用户", -1);
        }

        foreach ($uids as $uid) 
        {
            if ( $uid == kali::$auth->uid ) 
            {
                kali::$auth->logout();
                cls_msgbox::show('注销登录', '成功退出登录！', './');
            }
            // 删除用户SESSION信息, 让用户推出登录
            mod_session::del_user_session($uid);
            kali::$auth::instance($uid)->del_cache();
        }

        cls_msgbox::show('系统提示', '终断成功', req::forword());
    }
}
