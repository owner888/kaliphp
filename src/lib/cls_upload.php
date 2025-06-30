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

use kaliphp\req;
use kaliphp\lang;
use kaliphp\config;
use kaliphp\util;
use Exception;

/**
 * 文件上传码类
 *
 * $FILES[ 'file' ][ 'error' ]一共有7种类型：
 * 1、UPLOAD_ERR_OK
 * 其值为 0，没有错误发生，文件上传成功。
 * 2、UPLOAD_ERR_INI_SIZE
 * 其值为 1，上传的文件超过了 php.ini 中 upload_max_filesize选项限制的值。
 * 3、UPLOAD_ERR_FORM_SIZE
 * 其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
 * 4、UPLOAD_ERR_PARTIAL
 * 其值为 3，文件只有部分被上传。
 * 5、UPLOAD_ERR_NO_FILE
 * 其值为 4，没有文件被上传。
 * 6、UPLOAD_ERR_NO_TMP_DIR
 * 其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
 * 7、UPLOAD_ERR_CANT_WRITE
 * 其值为 7，文件写入失败。PHP 5.1.0 引进。
 *
 * @version $Id$  
 */
class cls_upload
{
    public static $config = [];

    public static function _init()
    {
        self::$config = config::instance('upload')->get();
    }

    /**
     * 普通上传
     * 
     * @param string $formname
     * @param string $dir
     * @param int $thumb_width
     * @param float $thumb_height
     * @return array
     */
    public static function upload( $formname = 'file', $dir = 'image', $thumb_w = 0, $thumb_h = 0 )
    {
        $dir = self::filter_path($dir);

        // 上传成功
        if ( req::is_upload_file($formname) )    
        {
            $upload_dir = self::$config['filepath']."/{$dir}";

            // 目录不存在则生成
            if ( !util::path_exists($upload_dir) ) 
            {
                throw new Exception(lang::get('upload_not_exist'));
            }

            $allowed_types = explode('|', self::$config['allowed_types']);
            if ( !req::check_subfix($formname, $allowed_types) )
            {
                throw new Exception(lang::get('upload_invalid_filetype'));
            }    

            $filesize = req::get_file_info($formname, 'size');    
            $realname = req::get_file_info($formname, 'name');
            $file_ext = req::get_shortname($formname);
            
            // 判断文件大小
            if ( self::$config['max_size'] != 0 ) 
            {
                $max_size = self::$config['max_size'] * 1024;
                if ( $filesize > $max_size ) 
                {
                    throw new Exception(lang::get('upload_invalid_filesize'));
                }
            }

            $filename = md5_file(req::get_tmp_name($formname)).".".$file_ext;
            //$filename = uniqid().'.'.$file_ext;

            // 如果需要分隔目录上传
            if ( self::$config['dir_num'] > 0 ) 
            {
                $dir_num = util::str2number($filename, self::$config['dir_num']);
                util::path_exists($upload_dir.'/'.$dir_num);                    
                $filename = $dir_num.'/'.$filename;
            }

            if ( req::move_upload_file($formname, $upload_dir.'/'.$filename) ) 
            {
                @chmod($upload_dir.'/'.$filename, 0777);

                $filelink = self::$config['filelink'].'/'.$dir.'/'.$filename;

                if ( $thumb_w > 0 || $thumb_h > 0 ) 
                {
                    list( $filename, $filelink ) = self::thumb( $upload_dir, $filename, $file_ext, $thumb_w, $thumb_h );
                }

                return array(
                    'realname' => $realname,
                    'filename' => $filename,
                    'filelink' => $filelink, 
                );
            }
        }
        else 
        {
            if ( req::$files[$formname]['error'] == UPLOAD_ERR_INI_SIZE || req::$files[$formname]['error'] == UPLOAD_ERR_FORM_SIZE )
            {
                throw new Exception(lang::get('upload_invalid_filesize'));
            }
        }
    }

    /**
     * HTML5 图片字节流方式上传
     * 
     * @param mixed $filedata
     * @param string $filetype
     * @param int $thumb_width
     * @param float $thumb_height
     * @return array
     */
    public static function upload_html5( $filedata, $dir = 'image', $thumb_w = 0, $thumb_h = 0 )
    {
        $dir = self::filter_path($dir);

        // 匹配出图片的格式
        if ( preg_match('/^(data:\s*image\/(\w+);base64,)/', $filedata, $result) )
        {
            $upload_dir = self::$config['filepath']."/{$dir}";

            // 目录不存在则生成
            if ( !util::path_exists($upload_dir) ) 
            {
                throw new Exception(lang::get('upload_not_exist'));
            }

            // 检查文件类型
            $file_ext = $result[2];
            $file_ext = $file_ext == 'jpeg' ? 'jpg' : $file_ext;
            $allowed_types = explode('|', self::$config['allowed_types']);
            if ( !in_array($file_ext, $allowed_types) )
            {
                throw new Exception(lang::get('upload_invalid_filetype'));
            }    

            // 把 data:image/jpeg;base64, 去掉
            $filedata = base64_decode(str_replace($result[1], '', $filedata));

            // 判断文件大小
            if ( self::$config['max_size'] != 0 ) 
            {
                $max_size = self::$config['max_size'] * 1024;
                if ( strlen($filedata) > $max_size ) 
                {
                    throw new Exception(lang::get('upload_invalid_filesize'));
                }
            }

            //$filename = uniqid().'.'.$file_ext;
            $filename = md5($filedata).".".$file_ext;

            // 如果需要分隔目录上传
            if ( self::$config['dir_num'] > 0 ) 
            {
                $dir_num = util::str2number($filename, self::$config['dir_num']);
                util::path_exists($upload_dir.'/'.$dir_num);                    
                $filename = $dir_num.'/'.$filename;
            }

            if ( util::put_file($upload_dir.'/'.$filename, $filedata) )
            {
                @chmod($upload_dir.'/'.$filename, 0777);
            }

            $filelink = self::$config['filelink']."/".$dir."/".$filename;

            if ( $thumb_w > 0 || $thumb_h > 0 ) 
            {
                list( $filename, $filelink ) = self::thumb( $upload_dir, $filename, $file_ext, $thumb_w, $thumb_h );
            }

            return array(
                'realname' => $filename,
                'filename' => $filename,
                'filelink' => $filelink, 
            );
        }
    }

    /**
     * 分片上传
     * 图片上传请调用上面两个方法，这里一般用于上传大文件
     * 
     * @param mixed $cleanup_target_dir     Remove old files
     * @param float $max_file_age           Temp file age in seconds 5x3600=18000
     * @return array
     */
    public static function upload_chunked($formname = 'file', $dir = 'file', $guid = 'guid', $chunk = 0, $chunks = 1, $thumb_w = 0, $thumb_h = 0, $cleanup_target_dir = true, $max_file_age = 18000)
    {
        $dir = self::filter_path($dir);

        // 生成上传分片的临时目录
        //$target_dir = ini_get("upload_tmp_dir")."/plupload";
        $target_dir = self::$config['filepath']."/tmp/{$guid}";
        $upload_dir = self::$config['filepath']."/{$dir}";
        // 目录不存在则生成
        if ( !util::path_exists($target_dir) ) 
        {
            throw new Exception(lang::get('upload_not_exist'));
        }

        if ( !util::path_exists($upload_dir) ) 
        {
            throw new Exception(lang::get('upload_not_exist'));
        }

        // 检查文件类型
        $file_ext = req::get_shortname($formname);
        $allowed_types = explode('|', self::$config['allowed_types']);
        if ( !in_array($file_ext, $allowed_types) )
        {
            throw new Exception(lang::get('upload_invalid_filetype'));
        }    

        $realname = req::get_file_info($formname, 'name');
        $partpath = $target_dir.'/'.$realname;              // 分片文件位置
        $realpath = $upload_dir.'/'.$realname;              // 合并分片后的文件位置

        // Remove old temp files
        if ( $cleanup_target_dir ) 
        {
            if (!is_dir($target_dir) || !$cleanup_dir = opendir($target_dir)) 
            {
                throw new Exception('Failed to open temp directory.');
            }

            while (($file = readdir($cleanup_dir)) !== false) 
            {
                $tmpfile_path = $target_dir . '/' . $file;

                // If temp file is current file proceed to the next
                if ($tmpfile_path == "{$partpath}_{$chunk}.part" || $tmpfile_path == "{$partpath}_{$chunk}.parttmp") 
                {
                    continue;
                }

                if (preg_match('/\.(part|parttmp)$/', $file) 
                    && file_exists($tmpfile_path) 
                    && (@filemtime($tmpfile_path) < time() - $max_file_age))
                {
                    @unlink($tmpfile_path);
                }
            }
            closedir($cleanup_dir);
        }

        // Open temp file
        if (!$out = @fopen("{$partpath}_{$chunk}.parttmp", "wb")) 
        {
            throw new Exception('Failed to open output stream.');
        }

        // 普通form表单上传
        if ( !empty(req::$files)) 
        {
            if ( !req::is_upload_file($formname) ) 
            {
                throw new Exception('Failed to move uploaded file.');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen(req::get_tmp_name($formname), "rb")) 
            {
                throw new Exception('Failed to open input stream.');
            }
        }
        // 字节流上传
        else 
        {
            if (!$in = @fopen("php://input", "rb")) 
            {
                throw new Exception('Failed to open input stream.');
            }
        }

        // 缓冲区写入文件
        while ($buff = fread($in, 4096))
        {
            fwrite($out, $buff);
        }

        // 关闭输出输入句柄
        @fclose($out);
        @fclose($in);

        // 分块临时文件改名为分块正式文件名
        rename("{$partpath}_{$chunk}.parttmp", "{$partpath}_{$chunk}.part");

        // 检查是否所有分块都已经上传完成
        // 这里有并发问题，当多个进程同时执行了上面的rename 然后到达这里，就会出现多个 $done = true;
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) 
        {
            if ( !file_exists("{$partpath}_{$index}.part") ) 
            {
                $done = false;
                break;
            }
        }

        // 所有分片已经上传完，这里会有并发问题
        if ( $done ) 
        {
            //解决多进程flock之后，写文件 0字节问题
            if (!$outlock = @fopen($realpath.'_lock', "wb"))
            {
                throw new Exception('Failed to open output stream.');
            }

            if ( flock($outlock, LOCK_EX | LOCK_NB))
            {
                if (!$out = @fopen($realpath, "wb"))
                {
                    throw new Exception('Failed to open output stream.');
                }
                for( $index = 0; $index < $chunks; $index++ ) 
                {
                    if (!$in = @fopen("{$partpath}_{$index}.part", "rb")) 
                    {
                        break;
                    }

                    while ($buff = fread($in, 4096)) 
                    {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$partpath}_{$index}.part");
                }
                flock($outlock, LOCK_UN);
                @fclose($outlock);
                @fclose($out);

            }
            else
            {
                //获取锁失败，直接返回，合并文件交给 其他进程处理
                @fclose($outlock);
                return;
            }
            //删除临时锁文件
            @unlink($realpath.'_lock');

            // 删除目录
            @rmdir($target_dir);

            // 保留真实名称
            $filename = md5_file($realpath).".".$file_ext;
            //$filename = uniqid().".".$file_ext;
            // 如果需要分隔目录上传
            if ( self::$config['dir_num'] > 0 ) 
            {
                $dir_num = util::str2number($filename, self::$config['dir_num']);
                if ( !util::path_exists($upload_dir.'/'.$dir_num) ) 
                {
                    throw new Exception(lang::get('upload_not_exist'));
                }
                $filename = $dir_num.'/'.$filename;
            }
            rename($realpath, "{$upload_dir}/{$filename}");
            //rename('tmp/104/84405b7dae5c5a13fe76a99de3a8293d.jpg', 'image/104/84405b7dae5c5a13fe76a99de3a8293d.jpg');

            // 判断文件大小
            if ( self::$config['file_max_size'] != 0 ) 
            {
                $max_size = self::$config['file_max_size'] * 1024;
                if ( filesize("{$upload_dir}/{$filename}") > $max_size ) 
                {
                    throw new Exception(lang::get('upload_invalid_filesize'));
                }
            }

            $filelink = self::$config['filelink'].'/'.$dir.'/'.$filename;

            if ( $thumb_w > 0 || $thumb_h > 0 ) 
            {
                list( $filename, $filelink ) = self::thumb( $upload_dir, $filename, $file_ext, $thumb_w, $thumb_h );
            }

            return array(
                'realname' => $realname,
                'filename' => $filename,
                'filelink' => $filelink,
                'realpath' => realpath(self::$config['filepath']),
                'file_ext' => $file_ext,
            );
        }
    }

    public static function thumb( $upload_dir, $filename, $file_ext = 'jpg', $thumb_w = 0, $thumb_h = 0 )
    {
        // $pathinfo = getimagesize($upload_dir.'/'.$filename);
        // $width  = $pathinfo[0];
        // $height = $pathinfo[1];

        // 缩略图的临时目录
        $filepath_tmp = self::$config['filepath'].'/tmp';
        // 缩略图的临时文件名
        $filename_tmp = md5($filename).'.'.$file_ext;

        $img = new cls_image( $upload_dir.'/'.$filename );

        if ( $thumb_w > 0 && $thumb_h > 0 ) 
        {
            $img->thumb( $thumb_w, $thumb_h, $filepath_tmp.'/'.$filename_tmp, 'wh' ); 
        }
        // 只设置了宽度，自动计算高度
        elseif ( $thumb_w > 0 && $thumb_h == 0 ) 
        {
            $img->thumb( $thumb_w, $thumb_h, $filepath_tmp.'/'.$filename_tmp, 'w' ); 
        }
        // 只设置了高度，自动计算宽度
        elseif ( $thumb_h > 0 && $thumb_w == 0 ) 
        {
            $img->thumb( $thumb_w, $thumb_h, $filepath_tmp.'/'.$filename_tmp, 'h' ); 
        }

        //@chmod($filepath_tmp.'/'.$filename_tmp, 0777);

        $filename = md5_file($filepath_tmp.'/'.$filename_tmp).".".$file_ext;
        //$filename = uniqid().'.'.$file_ext;

        // 如果需要分隔目录上传
        if ( self::$config['dir_num'] > 0 ) 
        {
            $dir_num = util::str2number($filename, self::$config['dir_num']);
            if ( !util::path_exists($upload_dir.'/'.$dir_num) ) 
            {
                throw new Exception(lang::get('upload_not_exist'));
            }
            $filename = $dir_num.'/'.$filename;
        }

        rename($filepath_tmp.'/'.$filename_tmp, "{$upload_dir}/{$filename}");

        $filelink = self::$config['filelink'].'/image/'.$filename;
        return array($filename, $filelink);
    }

    //去掉前端传过来的路径
    public static function filter_path($dir)
    {
        $dirs = [];
        array_map(function($a) use (&$dirs){
            if ( !empty($dir = preg_replace('#[\s\.]#', '', $a)) )
            {
                $dirs[] = $dir;
            }
        }, explode('/', $dir));

        return implode('/', $dirs);
    }
    
    public static function file_manager()
    {

    }
}

