<?php
namespace control;

use kaliphp\db;
use kaliphp\req;
use kaliphp\tpl;
use kaliphp\log;
use kaliphp\kali;
use kaliphp\util;
use kaliphp\config;
use kaliphp\lib\cls_page;
use kaliphp\lib\cls_msgbox;
use kaliphp\lib\cls_upload;


// 5 minutes execution time
@set_time_limit(5 * 60);

class ctl_upload
{
    public function __construct()
    {
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * 普通上传
     */
    public function upload()
    {
        $formname   = req::item("formname", 'file');    // 上传表单字段
        $dir        = req::item("dir", 'tmp');          // 文件上传目录
        $thumb_w    = req::item("thumb_w", 0, 'int');   // 图片缩略图宽度
        $thumb_h    = req::item("thumb_h", 0, 'int');   // 图片缩略图高度

        try
        {
            $ret = cls_upload::upload($formname, $dir, $thumb_w, $thumb_h);
            util::return_json(array(
                'code' => 0,
                'msg'  => 'successful',
                'data' => $ret,
            ));

        }
        catch (Exception $e)
        {
            util::return_json(array(
                'code' => -1,
                'msg'  => $e->getMessage(),
            ));
        }
    }
    
    /**
     * HTML5 图片字节流方式上传
     */
    public function upload_html5()
    {
        $dir        = req::item("dir", 'image');        // 文件上传目录
        $thumb_w    = req::item("thumb_w", 0, 'int');   // 图片缩略图宽度
        $thumb_h    = req::item("thumb_h", 0, 'int');   // 图片缩略图高度
        try
        {
            $ret = cls_upload::upload_html5(req::post('filedata'), $dir, $thumb_w, $thumb_h);
            util::return_json(array(
                'code' => 0,
                'msg'  => 'successful',
                'data' => $ret,
            ));
        }
        catch (Exception $e)
        {
            util::return_json(array(
                'code' => -1,
                'msg'  => $e->getMessage(),
            ));
        }
    }

    /**
     * 分片上传
     */
    public function upload_chunked()
    {
        $formname   = req::item("formname", 'file');    // 上传表单字段
        $dir        = req::item("dir", 'file');         // 文件上传目录
        $guid       = req::item("guid");                // 分片文件关联随机字符串
        $chunk      = req::item("chunk",   0, 'int');   // 当前分片序号
        $chunks     = req::item("chunks",  1, 'int');   // 总分片个数
        $thumb_w    = req::item("thumb_w", 0, 'int');   // 图片缩略图宽度
        $thumb_h    = req::item("thumb_h", 0, 'int');   // 图片缩略图高度

        try
        {
            $ret = cls_upload::upload_chunked($formname, $dir, $guid, $chunk, $chunks, $thumb_w, $thumb_h);
            util::return_json(array(
                'code' => 0,
                'msg'  => 'successful',
                'data' => $ret,
            ));
        }
        catch (Exception $e)
        {
            util::return_json(array(
                'code' => -1,
                'msg'  => $e->getMessage(),
            ));
        }
    }
}
