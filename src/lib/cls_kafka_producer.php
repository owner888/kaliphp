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

/**
 * Kafka 生产类 
 * Kafka 操作文档: https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/book.rdkafka.html
 * Kafka 异步操作类: https://github.com/weiboad/kafka-php
 * 一个实现挺好的类: https://github.com/qkl9527/php-rdkafka-class
 * ex:
   cls_kafka_producer::instance()->send( 'test', 'hello' );
 * 
 * @version 1.0.0
 */
class cls_kafka_producer
{
    public static $config = [];

    /**
     * @var RdKafka\Producer
     */
    private $handler;

    /**
     * @var RdKafka\Conf
     */
    private $conf;

    /**
     * @var RdKafka\TopicConf
     */
    private $topic_conf;

    /**
     * @var RdKafka\Producer\newTopic
     */
    private $topic;

    private static $_instance;

    /**
     * 配置参数
     */
    private static $def_config;
    private static $broker_config;

    private $topic_name = null;

    public static function _init(): void
    {
        self::$config = config::instance('cache')->get();
    }

    /**
     * @param string $name  kafka
     * @return self
     */
    public static function instance(array $config = null ): self
    {
        if (!isset(self::$_instance))
        {
            // 没有传配置则调用系统配置好的
            if ( $config === null ) 
            {
                self::$def_config    = self::$config['kafka']['def_config'];
                self::$broker_config = self::$config['kafka']['broker_config'];
            }
            else 
            {
                self::$def_config    = $config['def_config'];
                self::$broker_config = $config['broker_config'];
            }

            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->conf = new \RdKafka\Conf();

        foreach (self::$def_config as $k => $v) 
        {
            $this->conf->set($k, $v);
        }

        // 在 set_topic 方法 new
        //$this->handler = new \RdKafka\Producer($this->conf);

        $this->topic_conf = new \RdKafka\TopicConf();
        // -1必须等所有brokers确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
        $this->topic_conf->set('request.required.acks', self::$broker_config['request.required.acks']);
    }

    /**
     * 动态添加Broker服务端
     *
     * @return self
     */
    public function add_brokers( string $brokers = null ): self
    {
        $this->handler->addBrokers($brokers ? : self::$def_config['metadata.broker.list']);

        return $this;
    }

    /**
     *  消息回调
     *
     *  ex:
     *  cls_kafka_producer::instance()->set_msg_callback(function($kafka, $message) {
     *      if ( $message->err ) 
     *      {
     *          echo $message->errstr(), "\n";
     *      }
     *      echo var_export($message, true).PHP_EOL;
     *      //file_put_contents("./dr_cb.log", var_export($message, true), FILE_APPEND);
     *  })
     */
    public function set_msg_callback( \Closure $callback ): self
    {
        if ( is_null($callback) ) 
        {
            return $this;
        }

        $this->conf->setDrMsgCb(function ($kafka, $message) use ($callback) {
            call_user_func_array($callback, [$kafka, $message]);
        });

        return $this;
    }

    /**
     *  错误回调
     *
     *  ex:
        cls_kafka_producer::instance()->set_err_callback(function($kafka, $err, $reason) {
            sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason).PHP_EOL;
            //file_put_contents("./err_cb.log", sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason).PHP_EOL, FILE_APPEND);
        })
     */
    public function set_err_callback( \Closure $callback ): self
    {
        if ( is_null($callback) ) 
        {
            return $this;
        }

        $this->conf->setErrorCb(function ($kafka, $err, $reason) use ($callback) {
            call_user_func_array($callback, [$kafka, $err, $reason]);
        });

        return $this;
    }

    public function set_require_ack( int $required_acks = -1 ): self
    {
        $this->topic_conf->set('request.required.acks', $required_acks);

        return $this;
    }

    /**
     * 添加主题
     *
     * @return self
     */
    public function set_topic( string $topic_name ): self
    {
        if ( $this->topic_name != $topic_name ) 
        {
            $this->topic_name = $topic_name;
            $this->handler = new \RdKafka\Producer($this->conf);
            $this->topic = $this->handler->newTopic($topic_name, $this->topic_conf);
        }

        return $this;
    }

    /**
     * 发送消息 
     * 
     * @param string  $topic_name    主题名称 
     * @param string  $payload       消息内容 
     * @param array   $headers       消息头部 
     * @param integer $partition     分区 RD_KAFKA_PARTITION_UA 自动选择分区
     * 
     * @return bool
     */
    public function send(string $topic_name, string $payload, string $key = null, array $headers = null, $partition = RD_KAFKA_PARTITION_UA)
    {
        $this->set_topic($topic_name);

        if ( !$headers ) 
        {
            $this->topic->produce($partition, 0, $payload, $key);
        }
        else 
        {
            $this->topic->producev($partition, 0, $payload, $key, $headers);
        }

        while(($len = $this->handler->getOutQLen()) > 0)
        {
            $this->handler->poll(50);
        }

        return true;
    }

    /**
     * 获取到topic下可用的partitions 
     * 
     * @param mixed $topic_name topic_name 
     * 
     * @return array
     */
    public function get_avaliable_partitions($topic_name): array
    {

        return [];
    }

    /**
     * 调用Kafka其他方法
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->handler, $method], $arguments);
    }
}
