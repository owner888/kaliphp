<?php

namespace Tests;

use kaliphp\req;
use kaliphp\kali;
use kaliphp\util;

define('APPPATH', __DIR__);
define('ENVPATH', __DIR__ .'/.env');

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * 测试用
 */
kali::registry();

// 运行MVC，不走 MVC 可以只 kali::registry() 即可
kali::run();

class ctl_index 
{
	public function index()
	{
        $data['item'] = req::item();
        $data['headers'] = req::headers();

		util::response_data(0, 'success', $data);
	}
}

class ctl_unit_test extends ctl_index
{
	function test()
	{
		return $this->index();
	}
}


