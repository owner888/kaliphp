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

/**
 * Kafka 消费类 
 * Kafka 操作文档: https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/book.rdkafka.html
 * Kafka 异步操作类: https://github.com/weiboad/kafka-php
 * 一个实现挺好的类: https://github.com/qkl9527/php-rdkafka-class
 * ex:
   cls_kafka_consumer ::instance()
       //->set_topic('test', 0, RD_KAFKA_OFFSET_STORED)
       ->subscribe(['test'])
       ->consumer(function($message){
           print_r($message);
       });
 * 
 * @version 1.0.0
 */
class cls_kafka_consumer
{
    public static $config = [];

    /**
     * @var RdKafka\Consumer(low level) Or RdKafka\KafkaConsumer(high level) Or RdKafka\Queue(low level)
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
     * @var RdKafka\Consumer\newTopic
     */
    private $topic;

    private static $_instance;

    /**
     * 配置参数
     */
    private static $def_config;
    private static $broker_config;

    //public $topics = [];
    //public $partitions = [];
    //public $offsets = [];

    private $group_name       = 'my-group-name';
    private $topic_name       = null;
    private $partition        = null;
    private $offset           = 0;
    private $timeout          = 12 * 1000;
    private $timeout_callback = null;

    public static function _init()
    {
        self::$config = config::instance('cache')->get();
    }

    /**
     * @param string $name  kafka
     * @return self
     */
    public static function instance( array $config = null ): self
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
        // 设置一个默认值，用户可以省去 set_group 方法的调用
        $this->conf->set('group.id', $this->group_name);

        // 在 set_topic 方法 new
        //$this->handler = new \RdKafka\Consumer($this->conf);

        $this->topic_conf = new \RdKafka\TopicConf();
        // -1必须等所有brokers确认 1当前服务器确认 0不确认，这里如果是0回调里的offset无返回，如果是1和-1会返回offset
        //$this->topic_conf->set('request.required.acks', self::$broker_config['request.required.acks']);
        foreach (self::$broker_config as $k => $v) 
        {
            $this->topic_conf->set($k, $v);
        }
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
     * 重新分配回调 
     *
     * 只有高级消费 RdKafka\KafkaConsumer 有这个回调
     *
     * ex:
       ->set_rebalance_callback(function($kafka, $err, $partitions) {
           switch ($err) {
               case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                   // 告诉同一个消费组其他进程，当前分区已经占用
                   $kafka->assign($partitions);
                   break;

               case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                   // 告诉同一个消费组其他进程，当前分区已经释放
                   $kafka->assign(NULL);
                   break;

               default:
                   $kafka->assign(NULL);
                   throw new \Exception($err);
           }
       })
     *
     */
    public function set_rebalance_callback( \Closure $callback ): self
    {
        if ( is_null($callback) ) 
        {
            return $this;
        }

        $this->conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, $partitions) use ($callback) {
            call_user_func_array($callback, [$kafka, $err, $partitions]);
        });

        return $this;
    }

    /**
     * 设置消费组
     * 所有消费者使用同一个分组会自动选择不同分区进行消费，消费者多于分区数目会导致有的消费者闲置
     *
     * @param $group_name
     */
    public function set_group($group_name = 0): self
    {
        $this->group_name = $group_name;

        $this->conf->set('group.id', $this->group_name);

        return $this;
    }

    /**
     * 设置消费组
     * 所有消费者使用同一个分组会自动选择不同分区进行消费，消费者多于分区数目会导致有的消费者闲置
     *
     * @param $group_name
     */
    public function set_partition(int $partition = 0): self
    {
        $this->partition  = $partition;

        return $this;
    }

    /**
     * 设置消费超时时间 
     * 
     * @param int $timeout 超时时间(单位: ms) 
     * 
     * @return self
     */
    public function set_timeout(int $timeout = 12000): self
    {
        $this->timeout  = $timeout;

        return $this;
    }

    /**
     * 设置消费超时回调 
     * 
     * @param int $callback 回调函数 
     * 
     * @return self
     */
    public function set_timeout_callback(\Closure $callback): self
    {
        $this->timeout_callback  = $callback;

        return $this;
    }

    public function set_max_bytes(int $max_bytes = 102400): self
    {
        $this->max_bytes  = $max_bytes;

        return $this;
    }

    /**
     * 设置主题，低级消费 
     * 
     * @param string $topic_name topic_name 
     * @param int $partition partition 
     * @param int $offset offset  RD_KAFKA_OFFSET_BEGINNING  重头开始消费
     *                            RD_KAFKA_OFFSET_STORED     最后一条消费的offset记录开始消费
     *                            RD_KAFKA_OFFSET_END        最后一条消费
     *                            1000                       从1000条开始消费
     *                            rd_kafka_offset_tail(200)  最后200条记录开始消费
     * 
     * @return self
     */
    public function set_topic( string $topic_name, int $partition = 0, int $offset = RD_KAFKA_OFFSET_STORED ): self
    {
        $this->topic_name = $topic_name;
        $this->partition  = $partition;

        // low level
        //$this->handler = $this->conf->newQueue();
        $this->handler = new \RdKafka\Consumer($this->conf);
        $this->topic = $this->handler->newTopic($topic_name, $this->topic_conf);

        $this->topic->consumeStart($partition, $offset);

        return $this;
    }

    /**
     * 订阅主题，高级消费 
     */
    public function subscribe(array $topics): self
    {
        // consumer 方法需要通过 partition 是否为 null 来决定是高级模式还是低级模式
        $this->partition = null;

        // high level
        $this->handler = new \RdKafka\KafkaConsumer($this->conf);
        $this->handler->subscribe($topics);

        return $this;
    }

    /**
     * 消费者 (high level)
     * 
     * @param \Closure $handle handle 
     * 
     * @return void
     */
    public function consumer(\Closure $callback)
    {
        while (true) 
        {
            if ( $this->partition === null ) 
            {
                $message = $this->handler->consume($this->timeout);
            }
            else 
            {
                $message = $this->topic->consume($this->partition, $this->timeout);
            }

            if ( $message ) 
            {
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $callback($message);
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        //echo "No more messages; will wait for more\n";
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        if ( $this->timeout_callback ) 
                        {
                            call_user_func($this->timeout_callback);
                        }
                        //echo "Timed out\n";
                        break;
                    default:
                        throw new \Exception($message->errstr(), $message->err);
                        break;
                }
            }
        }
    }

    /**
     * 查看服务器元数据（topic/partition/broker）
     *
     * @return void
     */
    public function stats()
    {
        $this->handler = new \RdKafka\Consumer($this->conf);

        $all = @$this->handler->metadata(true, NULL, 60e3);
        $topics = $all->getTopics();
        foreach ($topics as $topic) 
        {
            $topicName = $topic->getTopic();
            if ($topicName == "__consumer_offsets") 
            {
                continue ;
            }

            $partitions = $topic->getPartitions();
            foreach ($partitions as $partition) 
            {
                //$rf = new \ReflectionClass(get_class($partition));
                //foreach ($rf->getMethods() as $f) 
                //{
                //print_r($f);
                //}
                //die();

                $topPartition = new \RdKafka\TopicPartition($topicName, $partition->getId());
                echo "topic: ".$topPartition->getTopic()." - partition: ".$partition->getId()." - "."offset: ".$topPartition->getOffset().PHP_EOL;
            }
        }
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
