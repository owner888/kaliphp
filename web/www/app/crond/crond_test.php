<?php
// 自运行
// php crond_test.php
//require_once __DIR__ . '/../../vendor/autoload.php';

use kaliphp\req;
use kaliphp\util;
//use kaliphp\kali;
//kali::registry();

$time_start = microtime(true);

// 支持参数获取，操作如下
//php crond_test.php --name=kaka
//echo req::item('name')."\n";

$lock_key = 'crond_test';
if ( !util::lock($lock_key))
{
    echo "上锁失败，有任务正在执行\n";
    return;
}

echo "上锁成功，执行任务\n";

// 执行完任务，解锁
if ( util::unlock($lock_key))
{
    echo "解锁成功，任务完成\n";
}


$size = memory_get_usage();
$unit = array('b','kb','mb','gb','tb','pb'); 
$memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
$time = microtime(true) - $time_start;
echo "Done in $time seconds\t $memory\n";
