<?php
// Socket相关配置
return [
    'type'       => SOL_TCP,     // 类型：SOL_UDP、SOL_TCP
    'host'       => '127.0.0.1',
    'port'       => 9797,
    'timeout'    => 3000,        // 毫秒、3s
    'auto-throw' => true,        // 是否抛异常
];
