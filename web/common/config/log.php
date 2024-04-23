<?php

defined('DEBUG')   or define('DEBUG',   100);
defined('INFO')    or define('INFO',    200);
defined('NOTICE')  or define('NOTICE',  250);
defined('WARNING') or define('WARNING', 300);
defined('ERROR')   or define('ERROR',   400);

// 日志相关配置
return [
    //错误类型
    'log_type'          => 'file',
    //错误级别
    'log_threshold'     => [ERROR, WARNING, NOTICE, DEBUG, INFO],
    //错误日期格式
    'log_date_format'   => 'Y-m-d H:i:s',
    'log_chrome'        => false,
    // 那些请求方法提交的数据会被记录
    'log_request_methods'  => [
        //'*',
        //'GET', 'POST', 'PUT', 'DELETE',
        'POST',
    ],
    // 那些请求URL提交的数据会被记录
    'log_request_uris'  => [
        //'ct=index&ac=index',
        '*',
    ],
    //MYSQL慢查询阀值
    'slow_query'        => 1000,
];
