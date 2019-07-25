<?php
return [

    'webroot' => '@web@',

    // 访问安全配置
    'security' => [
        // 登录相关的安全
        'validate' => [
            'image_code'  => false,
            'mfa_code'    => false,
            'third_login' => false,
        ],

        // 指定某些IP允许开启调试，数组格式为 ['ip1', 'ip2'...]
        'safe_client_ip' => [
            '127.0.0.1',
            '101.1.18.36'
        ],
        // IP白名单
        'ip_whitelist' => [],
        // IP黑名单
        'ip_blacklist' => [],
        // 国家白名单
        'country_whitelist' => [],
        // 国家黑名单
        'country_blacklist' => [],
        // 伪密码登录可以查看的栏目
        'seclogin' => 'content-index,content-add,content-edit,content-del,category-index,category-add,category-edit,category-del,member-index,member-add,member-edit,member-del,admin-editpwd,admin-mypurview'
    ],

    // 访问权限设置
    'purview' => [
        // 权限池，已经抛弃
        //'allowpool' => 'admin',
        // 验证类型: session、cookie
        'auttype'    => 'session',
        // 未登录跳转地址
        'login_url'  => '?ct=index&ac=login',
        // 手工指定登录后跳转到的地址
        'return_url' => '?ct=index&ac=index',
        // 公开的控制器，不需登录就能访问
        'public'     => [
            'index' => [
                'document', 'login', 'logout', 'validate_image', 'send_code', 'reset_pwd', 'login_otp',
                //'demo' 
            ],
            'otp_enable' => [
                'authentication', 'install_app', 'bind'
            ],
        ],
        // 保护的控制器，会员登录后都能访问
        'protected'  => [
            'index' => [
                'index', 'adminmsg'
            ],
            'admin' => [
                'editpwd', 'mypurview'
            ]
        ],
        // 隐私的控制器，会员登录后拥有权限的可以访问
        'private'    => [],
    ],

    'websocket' => [
        'enable' => true,           // 是否打开websocket功能
        'scheme' => 'ws',           // ws、wss
        'host'   => '127.0.0.1',    // wss.kaliphp.com
        'port'   => '9527',         // 端口
        //'url'  => 'wss://wss.kaliphp.com:9528',
    ],
    
    // 语言包设置
    'language' => [
        'default'  => 'en',     // 默认语言包
        'fallback' => 'en',     // 默认语言包不存在的情况下调用这个语言包
        'locale'   => 'en_US',
        'always_load' => [      // 总是自动加载
            'common', 'form_validate', 'upload', 'menu', 'content'
        ] 
    ],

];
