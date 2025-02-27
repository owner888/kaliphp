<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       https://doc.kaliphp.com
 */

namespace kaliphp\lib;
use kaliphp\config;
use kaliphp\req;
use kaliphp\util;
use kaliphp\log;

/** 
 * Socket client 
 * exp:
 $resp = cls_socket::instance()->send([
     'cmd' => 'type',
     'msg' => 'hello',
 ]);
 var_dump($resp); 
 * @version $Id$  
 */
class cls_socket
{
    private static $_instances = [];

    /**
     * @var resource
     */
    private $handler;
    private $connect;

    /**
     * @param string $name
     *
     * @return Socket
     */
    public static function instance($name = 'socket', ?array $config = null)
    {
        if (!isset(self::$_instances[$name]))
        {
            // 没有传配置则调用系统配置好的
            if ( $config === null ) 
            {
                $config = config::instance('socket')->get();
            }

            self::$_instances[$name] = new self($config);
        }
        return self::$_instances[$name];
    }

    public function __construct($config)
    {
        $this->connect = $config;
    }

     /**
     * 选择库
     *
     * @param $name
     *
     * @return Socket
     */
    public function choose($name)
    {
        return self::instance($name);
    }

    /**
     * 创建handler
     */
    private function connect()
    {
        $config = $this->connect;
        if ($config['type'] == SOL_UDP)
        {
            $type = SOCK_DGRAM;
        } 
        else 
        {
            $type = SOCK_STREAM;
        }
        $auto_throw = $config['auto-throw'] ?? true;
        if( ($this->handler = socket_create(AF_INET, $type, $config['type'] ?: SOL_TCP)) === false) 
        {
            if ($auto_throw) 
            {
                throw new \Exception($config['type'], 4001);
            } 
            else 
            {
                log::warning(errorhandler::fmt_code(4001, $config['type']), 'SOCKET');
                return false;
            }
        }

        if ($config['timeout'])
        {
            socket_set_option($this->handler, SOL_SOCKET, SO_RCVTIMEO, [
                "sec"  => $config['timeout']/1000, 
                "usec" => $config['timeout']%1000
            ]);
        }

        if (@socket_connect($this->handler, $config['host'], $config['port']) === false) 
        {
            if ($auto_throw)
            {
                throw new \Exception(serialize([$config['host'], $config['port']]), 4002);
            } 
            else 
            {
                log::warning(errorhandler::fmt_code(4002, [$config['host'], $config['port']]), 'SOCKET');
                return false;
            }
        }

        return true;
    }

    /**
     * 发送buff
     *
     * @param string|array $buff
     *
     * @return bool
     */
    public function send_buff($buff)
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        if (is_array($buff))
        {
            $buff = json_encode($buff);
        }

        $len = strlen($buff);
        if ( $len != @socket_send($this->handler, $buff, $len, 0))
        {
            throw new \Exception('', 4003);
        }

        return true;
    }

     /**
     * 收Buff
     *
     * @return bool|mixed|string
     */
    public function rev_buff()
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        $n_cnt = @socket_recv($this->handler, $buf, 4, 0);
        // timeout
        if ($n_cnt === false)
        {
            return -1;
        }

        if ($n_cnt != 4) 
        {
            return false;
        }

        // 解包
        $ret  = unpack("Npack_len", $buf);
        $len  = $ret['pack_len'];
        $data = '';
        $recvlen = $len;
        $i = 0;
        while ($recvlen > 0) 
        {
            if (++$i == 100)
            {
                throw new \Exception('', 4003);
            }

            $n_cnt = @socket_recv($this->handler, $buf, $recvlen, 0);
            $data .= $buf;
            $recvlen -= $n_cnt;
        }

        //$data = chop($data);
        // 如果是json string
        if ($result = json_decode($data, true))
        {
            return $result;
        } 
        else 
        {
            return $data;
        }
    }

    /**
     * 发送socket请求
     * request和response，相当于http请求
     *
     * @param string|array $buff
     *
     * @return bool|mixed|string
     */
    public function send($buff)
    {
        if (!$this->handler)
        {
            $this->connect();
        }

        if ($this->send_buff($buff))
        {
            return $this->rev_buff();
        }

        return false;
    }
}
