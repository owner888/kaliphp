<?php
// 数据库相关配置
// mysql8.0 支持 php7.x 需要运行CREATE USER kaliphp@"%" identified with mysql_native_password by 'kaliphp';
return [
    'user'       => $_ENV['DB_USERNAME'],
    'pass'       => $_ENV['DB_PASSWORD'],
    'name'       => $_ENV['DB_DATABASE'],
    'charset'    => 'utf8mb4',
    'collation'  => 'utf8mb4_general_ci',
    'prefix'     => $_ENV['DB_PREFIX'],
    // 是否启用长链接，不要启用，mysqli的长链问题很多
    'keep-alive' => false,
    // 是否对SQL语句进行安全检查并处理，在插入十万条以上数据的时候会出现瓶颈
    'safe_test'  => true,
    // 慢查询阀值，秒
    'slow_query' => 0.5,
    'timeout'    => $_ENV["DB_TIMEOUT"],
    'host'       => [
        'master' => $_ENV['DB_MASTER_HOST'] . ':' .$_ENV['DB_MASTER_PORT'],
        'slave'  => [$_ENV['DB_SLAVE_HOST'] . ':' .$_ENV['DB_SLAVE_PORT']]
    ],
    'crypt_key'    => $_ENV['DB_CRYPT_KEY'],
    'crypt_fields' => [
        //'kaliphp_member' => [ 'name', 'age', 'email', 'address' ],
    ]
];
