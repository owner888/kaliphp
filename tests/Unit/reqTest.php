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

it('run in web', function () {
    // 发送请求
    $ret = @file_get_contents('http://localhost:8000');
    expect($ret)->toEqual('ok');
});

it('json request', function () {
    $url  = 'http://localhost:8000?ac=check_auth';
    $data = [
        'username' => 'username',
        'password' => 'password',
    ];
    $ret = util::http_request([
        'url'    => $url,
        'post'   => json_encode($data),
        'header' => ['Content-Type:application/json'] // 不传这个不会走 req 的 json 解析
    ]);

    $body = $ret['body'] ?? '';
    // print_r($body);

    expect($body)->toEqual($data['username']);
});

it('encrypt request', function () {
    $compress = $_ENV['USE_COMPRESS'];
    $url  = 'http://localhost:8000';
    $req_data = [
        'ac' => 'encrypt',
        'username' => 'username',
        'password' => 'password',
    ];
    $json = json_encode($req_data);
    $encrypt_str = cls_crypt::encode($json, $_ENV['CRYPT_KEY']);
    $ret = util::http_request([
        'url'    => $url,
        'post'   => $encrypt_str,
        'compress' => $compress,
        'header' => ['encrypt: 1']
    ]);

    $body = $ret['body'] ?? '';
    if ( $compress ) 
    {
        $body = @gzuncompress($body);
    }
    $body = cls_crypt::decode($body, $_ENV['CRYPT_KEY']);
    // print_r($body);
    $data = @json_decode($body, true);

    expect($data['code'] ?? 0)->toEqual(0);
    expect($data['msg'] ?? '')->toEqual('successful');
    expect($data['data']['username'] ?? '')->toEqual($req_data['username']);
});

