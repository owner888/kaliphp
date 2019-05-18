<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       http://kaliphp.com
 */

namespace kaliphp\lib;
use kaliphp\config;
use kaliphp\util;
use kaliphp\lib\cls_crypt;

/**
 * 消息通知类
 */
class cls_notice
{
    //public static $config = [];

    public static function _init()
    {
        //self::$config = config::instance('config')->get('notice');
    }

    /**
     * Websocket消息提示
     * 
     * @param mixed $uids       用户ID，多个用逗号隔开
     * @param string $title     消息提示标题
     * @param string $text      消息提示内容
     * @param string $url       消息提示对应的URL，如果在侧边栏里面，直接打开对应，否则新开一个网页
     * @return void
     */
    public static function websocket($uids = [], $title = '消息提示', $text = '', $url = '')
    {
        $uids = implode(',', $uids);
        $data = [
            'event' => 'sysmessage',
            'data' => [
                'uids'  => $uids,
                'title' => $title,
                'text'  => $text,
                'url'   => $url
            ]
        ];
        $data = json_encode($data);
        $key = config::instance()->get('api_key');
        $data = cls_crypt::encode($data, $key);
        $url = "http://127.0.0.1:9528";
        return util::http_post($url, ['data' => $data]);
    }
}
