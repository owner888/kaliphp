<?php
namespace control;
use kaliphp\kali;
use kaliphp\db;
use kaliphp\req;
use kaliphp\log;
use kaliphp\config;
use kaliphp\cache;
use kaliphp\util;

/**
 * WebSocket
 */
class ctl_websocket
{
    public static $config = [];

    public static function _init()
    {
        //self::$config = config::instance('config')->get('websocket');
    }

    public function connect()
    {
    
    }

    public function onopen()
    {
        req::item('connection')->send(json_encode(array(
            'Event'  => 'sysmessage',
            'Data'   => [
                'title' => '系统消息',
                'text'  => 'Hello ' . req::item('name'),
                'url'   => '',
                'time'  => date('Y-m-d H:i:s'),
            ]
        )));

        //echo req::item('connection')->realIP."\n";
    }

    public function demo()
    {
        echo __method__."\n";
        print_r(req::$gets);
        echo __method__."\n";
    }

}
