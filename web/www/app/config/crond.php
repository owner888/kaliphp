<?php
// CROND 定时器 配置文件
// 记得使用和web同样的用户避免生成的日志不同权限：crontab -uwww-data -e
// */1 * * * * /data/web/www.kaliphp.com/app/crond/index.php >> /data/web/www.kaliphp.com/app/data/crond.log

return [
    // 配置支持的格式
    'the_format' => [
        '*',        //每分钟
        '*:i',      //每小时 某分
        'H:i',      //每天 某时:某分
        '@-w H:i',  //每周-某天 某时:某分  0=周日
        '*-d H:i',  //每月-某天 某时:某分
        'm-d H:i',  //某月-某日 某时-某分
        'Y-m-d H:i',//某年-某月-某日 某时-某分
    ],
    // 配置执行的文件 db管理crond就不需要这个配置项了
    'the_time' => [
        // 每分钟
        //'*' => array('xxx.php'),
        // '*' => array('crond_test.php'),

        // 每小时 某分
        //'*:00' => array('xxx.php'),
        //'*:20' => array('xxx.php'),
        //'*:40' => array('xxx.php'),

        // 每天 某时:某分
        //'10:00' => array('xxx.php'),

        // 每周-某天 某时:某分
        //'@-0 01:30' => array('xxx.php', 'xxx.php','xxx.php'),

        // 每月-某天 某时:某分
        //'*-05 01:00' => array('xxx.php'),

        // 每年 某月-某日 某时-某分
        //'12-12 23:43' => array('xxx.php'),

        // 某年某月某日某时某分
        //'2008-12-12 23:43' => array('xxx.php'),
    ]
];
