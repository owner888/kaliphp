<?php
use kaliphp\req;
use kaliphp\util;
use kaliphp\kali;
use kaliphp\lib\cls_crypt;
use kaliphp\lib\cls_security;
// 在测试开始之前启动 Web 服务
beforeAll(function () {
    echo "Starting PHP built-in server...";
    $router = __DIR__ .'/../index.php';
    shell_exec('php -S localhost:8000 ' . $router . ' > /dev/null 2>&1 & echo $! > server.pid');
    sleep(1); // 给服务器一点时间来启动
});
// 在所有测试结束之后关闭 Web 服务
afterAll(function () {
    echo "Stopping PHP built-in server.";
    $pid = file_get_contents('server.pid');
    shell_exec('kill ' . $pid);
    unlink('server.pid');
});
kali::registry();
// 测试 req.php
it('run in web', function () {
    //发送请求
    $ret = file_get_contents('http://localhost:8000');
    expect($ret)->toBeJson();
});

it('json request', function () {
    $url  = 'http://localhost:8000?ct=index&ac=index';
    $data = [
        'a'  => 'sdjflkjalfdjlfjsladjflkdsjk\'sj<b>'. null . PHP_EOL . 'sdjfasjflksdjlkj<>?',
    ];

    $ret = util::http_request([
        'url'    => $url, 
        'post'   => json_encode($data),
        'header' => ['Content-Type:application/json']
    ]);
    $body = @json_decode($ret['body'], true);

    expect($body)->toBeArray();
    expect($body['data']['item']['a'])->toBe(cls_security::xss_clean($data['a']));
    expect($body['data']['headers']['Content-Type'])->toBe('application/json');
});

it('form request', function () {
    $url  = 'http://localhost:8000?ct=index&ac=index';
    $data = [
        'a'  => 'sdjflkjalfdjlfjs',
    ];

    $ret = util::http_request([
        'url'    => $url, 
        'post'   => $data,
        'header' => ['Content-Type:application/x-www-form-urlencoded']
    ]);
    $body = @json_decode($ret['body'], true);

    expect($body)->toBeArray();
    expect($body['data']['item']['a'])->toBe($data['a']);
    expect($body['data']['headers']['Content-Type'])->toBe('application/x-www-form-urlencoded');
});

it('encrypt request', function () {
    $url  = 'http://localhost:8000';
    $data = [
        'ct'  => 'unit_test',
        'ac'  => 'test',
        'a'  => 'sdjflkjalfdjlfjs',
        'b'  => 'sdjflkjalfdjlfjs>sjdfj>N',
    ];
    $json = json_encode($data);
    $encrypt_str = cls_crypt::encode($json, $_ENV['CRYPT_KEY']);

    $ret = util::http_request([
        'url'    => $url, 
        'post'   => $encrypt_str,
        'header' => ['encrypt:1']
    ]);
    // 不需要解密了测试方法没有对数据加密返回
    // $ret['body'] = cls_crypt::decode($ret['body'], $_ENV['CRYPT_KEY']);
    $body = @json_decode($ret['body'], true);

    expect($body)->toBeArray();
    expect($body['data']['item']['a'])->toBe($data['a']);
    expect($body['data']['item']['b'])->toBe($data['b']);
});

