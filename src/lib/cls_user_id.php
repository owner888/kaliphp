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

use kaliphp\db;

class cls_user_id 
{
    // 用户表
    public static $table = '#PB#_user_id';
    // 当前实例
    protected static $_instance;
    // 配置信息
    public static $config = [];

    public static function _init()
    {
    }

    // --------------------------------------------------------------------

    // 单例模式，生成实例
    public static function instance()
    {
        if (!self::$_instance instanceof self) 
        {
            self::$_instance = new static(self::$config);
        }
        return self::$_instance;
    }

    // --------------------------------------------------------------------

    // 获取随机的 user_id，因为生成的时候已经打乱了ID，所以这里按 id 正序直接取即可
    public static function get_random_user_id(): int
    {
        $user_id = (int) db::select("user_id")
            ->from(self::$table)
            ->where('status', '=', 0)
            ->order_by('id', 'asc')
            ->limit(1)
            ->as_field()
            ->execute();
        // 取完更新status
        if ( !empty($user_id) )
        {
            db::update(self::$table)
                ->set(['status' => 1])
                ->where('user_id', '=', $user_id)
                ->execute();
        }

        return $user_id;
    }

    // 每次随机从 user_id 表取 status=0 的数据，如果没有，获取最大的user_id，生成1W条数据，再取再返回
    public static function generate_user_id(): int
    {
        $user_id = self::get_random_user_id();
        if ($user_id == 0) 
        {
            $ret = cls_redis_lock::lock('generate_user_id', 0, 15);
            if (!$ret) 
            {
                // 没取到锁就直接返回，让客户端重新发起
                return $user_id;
            }

            // 生成 1000 条数据
            $count = 1000;
            $max_user_id = (int) db::select("user_id")
                ->from(self::$table)
                ->order_by('user_id', 'desc')
                ->limit(1)
                ->as_field()
                ->execute();

            $start_id = max($max_user_id + 1, 30000);
            $end_id   = $start_id - 1 + $count;
            $user_ids = [];
            for ($i = $start_id; $i <= $end_id ; $i++) 
            {
                $user_ids[] = $i;
            }

            shuffle($user_ids); // 数组打乱

            $data = [];
            foreach ($user_ids as $id) 
            {
                $data[] = [
                    'user_id' => $id,
                    'status'  => 0,
                    'addtime' => time()
                ];
            }

            db::insert(self::$table)
                ->values($data)
                ->columns(['user_id','status','addtime'])
                ->ignore()
                ->execute();

            $user_id = self::get_random_user_id();
        }

        return $user_id;
    }
}
