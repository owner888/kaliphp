<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

use kaliphp\kali;
use kaliphp\req;
use kaliphp\tpl;

define('SYS_DEBUG', true);
define('SYS_CONSOLE', false);
// dev pre pub
define('SYS_ENV', 'dev');
// app path
define('APPPATH', __DIR__.'/app');

# APP信息
$app_config = [
    'session_start'  => true,                               // 是否启用session
    'check_purview_handle' => ['model\mod_auth', 'auth'],   // 权限检查
    'menu_file'  => 'menu.xml',                             // 获取菜单和用户权限配置
];

kali::registry( $app_config );

if ( !(req::item('ct') == 'index' && req::item('ac') == 'index') )
{
    // 所有访问开启程序分析器
    //cls_profiler::instance()->enable_profiler(true);
}

tpl::assign('title', 'KaliPHP DEMO');
// 运行MVC
kali::run();
