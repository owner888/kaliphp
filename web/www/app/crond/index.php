#!/usr/bin/env php
<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use kaliphp\db;
use kaliphp\log;
use kaliphp\kali;
use kaliphp\config;

define('RUN_SHELL', true);
define('ENVPATH', __DIR__.'/../../../.env');
define('APPPATH', __DIR__.'/../');

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
    chdir(dirname(__FILE__));
}

kali::registry();
// 执行CROND
// kali::crond();
$index_time_start = microtime(true);

$list = db::select('*')
    ->from('#PB#_crond')
    ->where('status', '=', 1)
    ->limit(100) 
    ->execute();
if (empty($list)) 
{
    echo '计划任务为空';
    exit;
}

$job_list = [];
foreach ($list as $v) 
{
    $job_list[$v['runtime_format']][] = $v;
}

$config = config::instance('crond')->get();

// 提取要执行的文件
$exe_job = array();
foreach ($config['the_format'] as $format)
{
    $key = date($format, ceil($index_time_start));
    if (is_array(@$job_list[$key]))
    {
        $exe_job = array_merge($exe_job, $job_list[$key]);
    }
}

echo "\n" . date('Y-m-d H:i', time()), "\n\n";
$size = memory_get_usage();
$unit = array('b','kb','mb','gb','tb','pb'); 
$memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
echo "Start in $memory\n\n\n";


// 加载要执行的文件
foreach ($exe_job as $v)
{
    // 过滤掉不是 core/crond/ 目录下的文件，否则被上传到data目录的php就很危险了
    $commad_name = $v['filename'];

    $job_id = $v['id'];

    $path_file = kali::$base_root.DS.'crond'.DS.$commad_name;
    echo '  ', $commad_name,"\n";   

    $runtime_start = microtime(true);
    if( function_exists('pcntl_fork') )//支持多进程优先使用，防止某个crond中断导致其他的无法执行
    {
        $pid = pcntl_fork();    //创建子进程
        if( $pid == -1 ) //错误处理：创建子进程失败时返回-1.
        {
            die('Could not fork');
        } 
        else if( $pid ) //父进程会得到子进程号，所以这里是父进程执行的逻辑
        {
            //如果不需要阻塞进程，而又想得到子进程的退出状态，则可以注释掉pcntl_wait($status)语句，或写成：
            pcntl_wait($status, WNOHANG); //等待子进程中断，防止子进程成为僵尸进程。
        } 
        else //执行子进程逻辑
        {
            $cmd = sprintf('php %s', $path_file);
            exec($cmd, $result, $return);

            $lasttime = ceil($runtime_start);
            $runtime  = number_format(microtime(true) - $runtime_start, 3);
            db::init_db();
            //更新下时间到数据库
            db::update('#PB#_crond')->set([
                'lasttime' => $lasttime,
                'runtime'  => $runtime,
            ])->where('id', $job_id)->execute();
            // 这里用0表示子进程正常退出
            exit(0);
        }
    }
    else
    {
        $cmd = sprintf('php %s', $path_file);
        exec($cmd, $result, $return);
        
        $lasttime = ceil($runtime_start);
        $runtime = number_format(microtime(true) - $runtime_start, 3);

        db::update('#PB#_crond')->set([
            'lasttime' => $lasttime,
            'runtime'  => $runtime,
        ])->where('id', $job_id)->execute();
    }

    echo "\n\n";
}

$size = memory_get_usage();
$unit = array('b','kb','mb','gb','tb','pb'); 
$memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
$time = microtime(true) - $index_time_start;
echo "All done in $time seconds\t $memory\n";
