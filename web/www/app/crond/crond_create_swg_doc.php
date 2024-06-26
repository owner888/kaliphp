<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use kaliphp\req;
use kaliphp\kali;
use kaliphp\util;
use kaliphp\lib\cls_swg_analyser;
use OpenApi\Generator;

defined('ENVPATH') or define('ENVPATH', __DIR__.'/../../../.env');
defined('APPPATH') or define('APPPATH',  __DIR__.'/../');

// 注册框架：初始化路径、DB ...
kali::registry();

// 支持参数获取，操作如下
//php crond_test.php name=kaka
//echo req::item('name')."\n";

/*
swg简化写法使用说明
官方的库的语法过于复杂，导致写接口文档比写代码还要难，而且学习成本高，非常不好阅读和维护，所以修改了下库，做了一下简化，目前只支持常见的语法(如果还有其他比较常用的目前不能满足的，可以联系扩展)，如果接口是在太复杂只能用官方的语法编写接口

上传到服务后，一分钟会生成一次，如果没有上传代码权限，可以通过执行 `crond_create_swg_doc.php` 生成，`swagger-ui/dist/data/`下面会生成一份api.json 文件，上传上去，通知前端拉一下代码可以看到更新(前端配置一个vhost执行到/swagger-ui/dist/)

composer库 `composer require zircote/swagger-php:3.0.2`

标准写法参考文档：`https://www.sdk.cn/details/9pPQD6wqK09L8ozvNy`

官方文档地址：`https://zircote.github.io/swagger-php/`

写法如下
* [SWG]
* @tags  分组名称
* @title 这是标题，需要则写：获取我的列表
* @desc  这是长描述，需要则写
* @path   post /test/index/index/my_list
* @param  integer  $page:1   页数  required=true 
* @param  integer  $limit:10 每页个数
* @return object   $data    {"a":1, "b":"123", "c":[1, 2, 3]}
* [/SWG]

@param 的格式
格式 ：变量类型  + $变量名:默认值(默认值可以缺省)+变量说明+其他属性(可以省略，比如：required=true)

//默认需要验证登录
@path  post   /index/test/ 
//如果不需要验证登录信息
@path post   /index/test false


*/


$lock_key = basename(__FILE__, '.php');;
if ( !util::lock($lock_key))
{
    echo "上锁失败，有任务正在执行\n";
    return;
}

echo "上锁成功，执行任务\n";

//定时刷新下
if (defined(SWG_DIR) && is_dir(SWG_DIR)) 
{
    Logger::$debug_log = false;
    // 上线后要去掉旧项目的
    $path    = [APPPATH . '/control'];//指定多个也可以
    $openapi = Generator::scan($path,['analyser' => new cls_swg_analyser(), 'validate' => false]);
    $content = $openapi->toJson();    
    file_put_contents(SWG_DIR . '/api.json', $content);

    echo "刷新到swg文件成功" . PHP_EOL;
}

// 执行完任务，解锁
if ( util::unlock($lock_key) )
{
    echo "解锁成功，任务完成\n";
}

$size = memory_get_usage();
$unit = array('b','kb','mb','gb','tb','pb'); 
$memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
$time = microtime(true) - $time_start;
echo "Done in $time seconds\t $memory\n";
