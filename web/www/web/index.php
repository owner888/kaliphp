<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';

use kaliphp\kali;
use kaliphp\req;
use kaliphp\tpl;
// use kaliphp\lib\cls_profiler;

define('SYS_DEBUG', true);
define('SYS_CONSOLE', false);
define('SYS_ENV', 'dev'); // dev || pre || pub
define('APPPATH', __DIR__.'/../app');
define('SWG_DIR', __DIR__.'/../app/data');//配置下swgui的json文件存放文件夹路径即可

# APP信息
$app_config = [
    'session_start'  => true,                             // 是否启用session
    'check_purview_handle' => ['model\mod_auth', 'auth'], // 权限检查
    'menu_file'  => 'menu.xml',                           // 获取菜单和用户权限配置
];

// 注册框架：初始化路径、DB ...
kali::registry($app_config);

if ( !(req::item('ct') == 'index' && req::item('ac') == 'index') )
{
    // 所有访问开启程序分析器
    // cls_profiler::instance()->enable_profiler(true);
}

// 运行MVC，不走 MVC 可以只 kali::registry() 即可
kali::run();
