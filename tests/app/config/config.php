<?php
return [
    'request' => [
        'no_encrypt_actions' => [ // 容许不加密的 ct ac, 比如上传接口
            'test:swg',
            'upload:upload',
            'upload:upload_chunked'
        ],
        'encrypt_key'  => $_ENV['CRYPT_KEY'],
    ],
];
