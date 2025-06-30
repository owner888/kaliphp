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
    'mqtt' => [
        'server' => [
            'host' => '', 
            'port' => 31883, 
            'user' => 'testmqtt_langrensha', 
            'pass' => 'ErZDb07XXfZMlk7', 
            'keep-alive' => 60,
            'tls-crt' => [
                //'ca_file'   => '/path/to/ca.crt',
                //'cert_file' => '/path/to/client.crt',
                //'key_file'  => '/path/to/client.key',
                //'password'  => '/path/to/client.key',
            ],
            'tls-psk' => [
                //'psk' => '',
                //'identity' => '',
                //'ciphers' => null
            ]
        ]
    ],
    'kafka' => [
        'mode'    => 1,    // 1线下发送 2线上发送,
        'def_config' => [
            'metadata.broker.list' => '192.168.10.35:9092',
            'security.protocol'    => 'SASL_PLAINTEXT',
            'sasl.mechanisms'      => 'PLAIN',
            'sasl.username'        => 'test',
            'sasl.password'        => '123456',
        ],
        'broker_config' => [
            'request.required.acks'   => -1,                    // -1必须等所有brokers确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
            'auto.commit.enable'      => 0,                     // 在 interval.ms 的时间内自动提交确认，建议不要启动
            'auto.commit.interval.ms' => 100,                   // 自动提交时间
            'offset.store.method'     => 'broker',              // 设置offset存储: broker | file
            'offset.store.path'       => sys_get_temp_dir(),    // 如果offset存储为file，需要设置保存文件路径
            'auto.offset.reset'       => 'smallest',
        ],
        'online_push_topic' => ['shutdown_function'],           // 在线推的topic名称
        'shudtown_function_topic_name' => 'shutdown_function',
    ],
    'gearman' => [
        'servers' => [
            ['host' => '192.168.10.47', 'port' => 4730]
        ],
        //'options' => GEARMAN_CLIENT_NON_BLOCKING | GEARMAN_CLIENT_FREE_TASKS | GEARMAN_CLIENT_UNBUFFERED_RESULT,
    ],
    'session' => [
        'type'   => 'cache',      // session类型 default || cache
        'expire' => 1440,         // session 回收时间 默认24分钟:1440、一天:86400
    ]
];
