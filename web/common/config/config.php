<?php
return [
    'request' => [
        'no_encrypt_actions' => [ // 容许不加密的 ct ac, 比如上传接口
            'test:swg',
            'upload:upload',
            'upload:upload_chunked'
        ],
        'use_compress' => $_ENV['USE_COMPRESS'], // 是否压缩数据
        'use_encrypt'  => $_ENV['USE_CRYPT'],    // 是否强制加密
        'encrypt_key'  => $_ENV['CRYPT_KEY'],
    ],
    'spam_config' => [ //spam 的锁定时长默认为cache.php里面cache_time的值
        'phone_code' => [
            'label' => '手机验证码',
            'keys'  => [
                'phone' => ['label' => '电话号码', 'limit' => 60, 'interval' => 60], 
                'ip'    => ['label' => 'IP', 'limit' => 1000],
            ]
        ],
        'email_code' => [
            'label' => '邮箱验证码',
            'keys'  => [
                'email' => ['label' => '邮箱', 'limit' => 6, 'interval' => 60],
                'ip'    => ['label' => 'IP', 'limit' => 100],
            ]
        ],
        'check_user' => [
            'label' => '后台账号登录',
            'keys'  => [
                'account' => ['label' => '账号', 'limit' => 10],
                'ip'    => ['label' => 'IP', 'limit' => 100],
            ]
        ]

    ],
];
