<?php
namespace control;

use kaliphp\req;
use kaliphp\tpl;
use kaliphp\util;
use kaliphp\lib\cls_spam;

class ctl_spam
{
    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        tpl::assign('reqs', req::$forms);
    }

    public function index()
    {
        $keys = cls_spam::list_keys();
        tpl::assign('keys', $keys);

        tpl::display('spam.index.tpl');
    }

    /**
     * 获取spam系统键
     * @Author han
     */
    public function get_keys()
    {
        $module = req::item('module', '');
        $keys   = cls_spam::list_keys($module);

        util::return_json($keys);
    }

    /**
     * 获取spam数据
     * @Author han
     */
    public function get_data()
    {
        $key = req::item('key', '');
        $keyword = req::item('keyword', '');

        $ret = [];
        if( !empty($key) && !empty($keyword) )
        {
            $key = $key.':'.$keyword;
            if( false != ($ret = cls_spam::get($key)) && !empty($ret['timestamp']) )
            {
                $ret['timestamp'] = date('Y-m-d H:i:s', $ret['timestamp']);
            }

        }

        util::return_json($ret);
    }

    /**
     * 清除spam数据
     * @Author han
     */
    public function clear_data()
    {
        $key = req::item('key', '');
        $keyword = req::item('keyword', '');
        $auto_clear = req::item('auto_clear', 0);

        $ret = ['code' => 0, 'msg' => '请输入spam内容'];
        if( !empty($key) && !empty($keyword) )
        {
            $key = $key.':'.$keyword;
            cls_spam::clear($key, $auto_clear);
            $tmp = cls_spam::get($key);

            if( empty($tmp['total']) )
            {
                $ret = ['code' => 1, 'msg' => '删除成功'];
            }
            else
            {
                $ret = ['code' => -1, 'msg' => '删除失败'];
            }
        }

        util::return_json($ret);
    }
}
