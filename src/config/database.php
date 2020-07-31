<?php
// 数据库相关配置
// mysql8.0 支持 php7.x 需要运行CREATE USER kaliphp@"%" identified with mysql_native_password by 'kaliphp';
return [
    'user'       => 'kaliphp',
    'pass'       => 'kaliphp',
    'name'       => 'kaliphp',
    'charset'    => 'utf8',
    'collation'  => 'utf8_unicode_ci',
    'prefix'     => 'kali',
    // 是否启用长链接，不要启用，mysqli的长链问题很多
    'keep-alive' => false,
    'timeout'    => 5,
    // 是否对SQL语句进行安全检查并处理，在插入十万条以上数据的时候会出现瓶颈
    'safe_test'  => true,
    // 慢查询阀值，秒
    'slow_query' => 0.5,
    'host' => [
        'master' => '127.0.0.1:3306',
        'slave'  => ['127.0.0.1:3306']
    ],
    'crypt_key' => 'key',
    'crypt_fields' => [
        //'kaliphp_member' => [ 'name', 'age', 'email', 'address' ],
    ],
    // 'json_fields' => [
    //     'lrs_test' => ['json_field']
    // ]
];
