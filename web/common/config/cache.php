<?php
// 缓存相关配置
return [
    'enable'     => true,
    'prefix'     => $_ENV['CACHE_PREFIX'] ?? 'kaliphp',
    'cache_type' => 'redis',//redis file memcache
    'cache_time' => 7200,
    'cache_name' => $_ENV['CACHE_NAME'] ?? 'kaliphp_data',
    // 开启redis自动序列化存储
    'serialize'  => true,
    'memcache'   => [
        'servers' => [
            [
                'host'       => $_ENV['MEMCACHE_HOST'] ?? '127.0.0.1', 
                'port'       => $_ENV['MEMCACHE_PORT'] ?? 11211, 
                'weight'     => 1, 
                'keep-alive' => false
            ],
        ]
    ],
    // redis目前只支持单台服务器，使用短连接，长链接在php7以上有问题，经常会被莫名回收
    'redis' => [
        'server' => [
            'host'       => $_ENV['REDIS_HOST'] ?? '127.0.0.1', 
            'port'       => $_ENV['REDIS_PORT'] ?? 6379, 
            'pass'       => $_ENV['REDIS_PASSWORD'] ?? '', 
            'keep-alive' => false, 
            'timeout'    => 5,
            'dbindex'    => 1
        ]
    ],

    'session' => [
        'type'   => 'cache', // session类型 default || cache || mysql
        'expire' => 1440,    // session 回收时间 默认24分钟:1440、一天:86400
    ]
];
