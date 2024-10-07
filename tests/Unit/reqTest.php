<?php

use kaliphp\util;
use kaliphp\kali;
use kaliphp\lib\cls_crypt;

kali::registry();

// 在测试开始之前启动 Web 服务
beforeAll(function () {
    $router = __DIR__ .'/../index.php';
    shell_exec('php -S localhost:8000 ' . $router . ' > /dev/null 2>&1 & echo $! > server.pid');
    sleep(1); // 给服务器一点时间来启动
});

// 在所有测试结束之后关闭 Web 服务
afterAll(function () {
    $pid = (string) @file_get_contents('server.pid');
    shell_exec('kill ' . $pid);
    unlink('server.pid');
});

// it('run in web', function () {
//     // 发送请求
//     $ret = (string) @file_get_contents('http://localhost:8000');
//     expect($ret)->toBeString();
// });
//
// it('json request', function () {
//     $url  = 'http://localhost:8000?ct=index&ac=index';
//     $data = [
//         'username' => 'hello test',
//     ];
//
//     $ret = util::http_request([
//         'url'    => $url,
//         'post'   => json_encode($data),
//         'header' => ['Content-Type:application/json']
//     ]);
//     $body = (array) @json_decode($ret['body'], true);
//     expect($body)->toBeArray();
//     expect($body['data']['headers']['Content-Type'] ?? '')->toBeString('application/json');
// });

it('encrypt request', function () {
    $url  = 'http://localhost:8000?ac=encrypt';
    $data = [
        'username' => 'username',
        'password' => 'password',
    ];
    $json = json_encode($data);
    $encrypt_str = cls_crypt::encode($json, $_ENV['CRYPT_KEY']);

    $ret = util::http_request([
        'url'    => $url,
        'post'   => $encrypt_str,
        'header' => ['encrypt:1']
    ]);

    print_r($ret);

    // 不需要解密了测试方法没有对数据加密返回
    // $ret['body'] = cls_crypt::decode($ret['body'], $_ENV['CRYPT_KEY']);
    // $body = @json_decode($ret['body'], true);
    //
    // expect($body)->toBeArray();
    // expect($body['data']['item']['a'])->toBe($data['a']);
    // expect($body['data']['item']['b'])->toBe($data['b']);
});

