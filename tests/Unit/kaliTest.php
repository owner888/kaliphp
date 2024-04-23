<?php
namespace control;

use kaliphp\util;
use kaliphp\kali;
use Exception;

//定义测试的controller
class ctl_index 
{
	public function index()
	{
		throw new Exception("cli run index/index", 1);
	}
}

kali::registry();
// 测试 kali.php

it('run in cli', function () {
	kali::run();
})->throws(Exception::class, 'cli run index/index');

it('fmt_code', function (){
	$ret = kali::fmt_code(1001, [kali::$base_root]);
    expect($ret)->toBeString();
});


it('app_total', function (){
	$ret = kali::app_total();
    expect($ret)->toBeArray();
});




