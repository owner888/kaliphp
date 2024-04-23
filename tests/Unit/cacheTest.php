<?php
use kaliphp\util;
use kaliphp\kali;
use kaliphp\cache;

kali::registry();
// 测试 cache.php
it('set get del int value', function () {
    $key    = 'kaliphptest_cacheset1';
    $value  = 10;

    $result = cache::set($key, $value);
    expect($result)->toBe(true);
    $result = cache::get($key);
    expect($result)->toBe($value);
    $result = cache::del($key);
    expect($result)->not->toBeEmpty();
    $result = cache::has($key);
    expect($result)->toBe(false);
});

it('set get del array value', function () {
    $key    = 'kaliphptest_cacheset2';
    $value  = ['aa' => 1, 'bb' => 'ssss'];

    $result = cache::set($key, $value);
    expect($result)->toBe(true);
    $result = cache::get($key);
    expect($result)->toBe($value);
    $result = cache::del($key);
    expect($result)->not->toBeEmpty();
    $result = cache::has($key);
    expect($result)->toBe(false);
});

it('ttl', function () {
    $key    = 'kaliphptest_cacheset2';
    $value  = ['aa' => 1, 'bb' => 'ssss'];
    $ttl    = 10;

    $result = cache::set($key, $value, $ttl);
    expect($result)->toBe(true);
    $result = cache::ttl($key);
    expect($result)->toBe($ttl);
    $result = cache::del($key);
    expect($result)->not->toBeEmpty();
});

it('inc and dec', function () {
    $key    = 'kaliphptest_cacheset3';

    $result = cache::inc($key, 1);
    expect($result)->toBe(1);

    $result = cache::inc($key, 1);
    expect($result)->toBe(2);

    $result = cache::get($key);
    expect($result)->toBe(2);

    $result = cache::dec($key, 1);
    expect($result)->toBe(1);

    $result = cache::get($key);
    expect($result)->toBe(1);

    $result = cache::del($key);
    expect($result)->not->toBeEmpty();
});

