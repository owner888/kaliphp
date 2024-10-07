<?php

use kaliphp\cache;
use kaliphp\kali;

kali::registry();

it('set get del int value', function () {
    $key    = 'test::cache::set1';
    $value  = 10;

    $result = cache::set($key, $value);
    expect((bool)$result)->toBe(true);

    $result = cache::get($key);
    expect($result)->toBe($value);

    $result = cache::del($key);
    expect($result)->not->toBeEmpty();

    $result = cache::has($key);
    expect($result)->toBe(false);
});

it('set get del array value', function () {
    $key    = 'test::cache::set2';
    $value  = ['aa' => 1, 'bb' => 'ssss'];

    $result = cache::set($key, $value);
    expect((bool)$result)->toBe(true);

    $result = cache::get($key);
    expect($result)->toBe($value);

    $result = cache::del($key);
    expect($result)->not->toBeEmpty();

    $result = cache::has($key);
    expect($result)->toBe(false);
});

it('ttl', function () {
    $key    = 'test::cache::set2';
    $value  = ['aa' => 1, 'bb' => 'ssss'];
    $ttl    = 10;

    $result = cache::set($key, $value, $ttl);
    expect((bool)$result)->toBe(true);

    $result = cache::ttl($key);
    expect($result)->toBe($ttl);

    $result = cache::del($key);
    expect($result)->not->toBeEmpty();
});

it('inc and dec', function () {
    $key    = 'test::cache::set3';

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

