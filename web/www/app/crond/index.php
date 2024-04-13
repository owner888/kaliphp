#!/usr/bin/env php
<?php
namespace crond;

require_once __DIR__ . '/../../vendor/autoload.php';

use kaliphp\kali;
use kaliphp\autoloader;
// The _init() method will execute when the class is loaded.
autoloader::register();

define('RUN_SHELL', true);
define('SYS_DEBUG', true);
//dev pre pub
define('SYS_ENV', 'dev');
// app path
define('APPPATH', __DIR__.'/../');
define('SWG_DIR', __DIR__.'/../data');//配置下swgui的json文件存放文件夹路径即可

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
    chdir(dirname(__FILE__));
}

kali::registry();
// 执行CROND
kali::crond();
