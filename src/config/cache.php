<?php
// 缓存相关配置
return [
    'enable'     => true,
    'prefix'     => 'mc_df_',
    'cache_type' => 'redis',
    'cache_time' => 7200,
    'cache_name' => 'cfc_data',
    // 开启redis自动序列化存储
    'serialize'  => true,
    'memcache' => [
        'servers' => [
            ['host' => '127.0.0.1', 'port' => 11211, 'weight' => 1, 'keep-alive' => false],
        ]
    ],
    // redis目前只支持单台服务器，使用短连接，长链接在php7以上有问题，经常会被莫名回收
    'redis' => [
        'server' => ['host' => '127.0.0.1', 'port' => 6379, 'pass' => '', 'keep-alive' => false, 'dbindex' => 1]
    ],

    'session' => [
        'type'   => 'cache',      // session类型 default || cache || mysql
        'expire' => 1440,         // session 回收时间 默认24分钟:1440、一天:86400
    ]
];
