<?php
defined('NONE')      or define('NONE',      0);     // 不记录日志
defined('ALL')       or define('ALL',       99);    // 所有日志
defined('DEBUG')     or define('DEBUG',     100);   // 详细的Debug信息
defined('INFO')      or define('INFO',      200);   // 关键的事件或信息，如用户登录信息，SQL日志信息
defined('NOTICE')    or define('NOTICE',    250);   // 普通但重要的事件信息
defined('WARNING')   or define('WARNING',   300);   // 出现非错误的异常，示例：使用不推荐使用的API，使用不当的API
defined('ERROR')     or define('ERROR',     400);   // 运行时错误，不需要立即执行，但通常应记录和监视
defined('CRITICAL')  or define('CRITICAL',  500);   // 严重错误，示例：应用程序组件不可用，意外异常
defined('ALERT')     or define('ALERT',     550);   // 必须立即采取行动，示例：整个网站关闭，数据库不可用等，这应该触发SMS警报并唤醒您
defined('EMERGENCY') or define('EMERGENCY', 600);   // 紧急情况：系统不可用

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
