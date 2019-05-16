<?php
namespace app\config;
use kali\core\kali;

// 上传设置
return array(
    'upload' => array(
        'filepath'      => kali::$app_root.'/../uploads',
        'filelink'      => 'http://www3.phpcall.org/uploads',
        'dir_num'       => 128,     // 目录数量
        'max_size'      => 1024,    // 允许上传图片大小的最大值（单位 KB），设置为 0 表示无限制
        'file_max_size' => 0,       // 允许上传文件大小的最大值（单位 KB），设置为 0 表示无限制
        'max_width'     => 0,       // 图片的最大宽度（单位为像素），设置为 0 表示无限制
        'max_height'    => 0,       // 图片的最大高度（单位为像素），设置为 0 表示无限制
        'min_width'     => 0,       // 图片的最小宽度（单位为像素），设置为 0 表示无限制
        'min_height'    => 0,       // 图片的最小高度（单位为像素），设置为 0 表示无限制
        'detect_mime'   => true,    // 如果设置为 TRUE ，将会在服务端对文件类型进行检测，可以预防代码注入攻击
        'allowed_types' => 'jpg|gif|png|bmp|webp|mp4|zip|rar|gz|bz2|xls|xlsx|pdf|doc|docx',
        'tinypng' => array(
            'apikey' => 'XcXBJ6bDXyALbcp2HVhYadNMNI8qgfmh',
            'apiurl' => 'https://api.tinify.com/shrink',
        ),
    )
);

