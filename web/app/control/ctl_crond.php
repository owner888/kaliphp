<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\tpl;
use kaliphp\config;
use kaliphp\cache;
use kaliphp\log;

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
        $list = [];

        foreach (self::$config['the_time'] as $k => $v) 
        {
            foreach($v as $file)
            {
                $cache_key = md5($file);
                $lasttime = cache::get('crond_lasttime_'.$cache_key);
                $runtime = cache::get('crond_runtime_'.$cache_key);
                $list[] = [
                    'runtime_format' => $k,
                    'filename' => $file,
                    'lasttime' => $lasttime,
                    'runtime' => $runtime,
                ];
            }
        }

        tpl::assign('list', $list);
        tpl::display('crond.index.tpl');
        exit();
    }

}
