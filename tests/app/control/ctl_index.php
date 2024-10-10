<?php
namespace control;

use kaliphp\req;
use kaliphp\resp;

class ctl_index
{
    public static $config = [];

    public static function _init()
    {   
    }

    public function index()
    {
        echo "index";
    }

    public function check_auth()
    {
        echo req::item('username');
    }

    public function encrypt()
    {
        resp::response(0, ['username' => 'test']);
    }
}
