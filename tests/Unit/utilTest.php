<?php
use kaliphp\util;
use kaliphp\kali;

kali::registry();
// 测试 util.php
it('lock', function () {
    $result = util::lock('xxxx1');
    expect($result)->toBeTrue();

});

it('unlock', function () {
    $result = util::unlock('xxxx1');
    expect($result)->toBeTrue();
});

it('file_ext', function () {
    $result = util::file_ext('xxxx1.log');
    expect($result)->toBe('log');

    $result = util::file_ext('xxxx1.log.ei');
    expect($result)->toBe('ei');
});

it('path_exists', function () {
    $result = util::path_exists(APPPATH . '/data/test');
    expect($result)->toBe(APPPATH . '/data/test');
});
