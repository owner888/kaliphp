<?php
return [
    'request' => [
        'no_encrypt_actions' => [ // 容许不加密的 ct ac, 比如上传接口
            'test:swg',
            'upload:upload',
            'upload:upload_chunked'
        ],
        'use_compress' => $_ENV['USE_COMPRESS'], // 是否压缩数据
        'use_encrypt'  => $_ENV['USE_CRYPT'],  // 是否强制加密
        'encrypt_key'  => $_ENV['CRYPT_KEY'],
    ],
];
