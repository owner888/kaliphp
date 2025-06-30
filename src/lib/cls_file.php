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

/** 
 * 文件操作类
 *
 * @version $Id$  
 */
class cls_file
{
    /**
     * 复制文件
     * @param string $source_file
     * @param string $target_file
     *
     * @return bool
     */
    public static function copy_file(string $source_file, string $target_file): bool
    {
        if (!file_exists($source_file))
        {
            return false;
        }

        // 目录不存在创建目录
        if (!is_dir(dirname($target_file)))
        {
            @mkdir(dirname($target_file));
        }

        return @copy($source_file, $target_file);
    }

    /**
     * 拷贝移动文件
     * 
     * @param string $source_file
     * @param string $target_file
     * @return bool
     */
    public static function move_file(string $source_file, string $target_file): bool
    {
        if (!file_exists($source_file))
        {
            return false;
        }
        if (!is_dir(dirname($target_file)))
        {
            @mkdir(dirname($target_file));
        }

        // 如果原目标有文件存在先删除
        if (is_file($target_file))
        {
            @unlink($target_file);
        }

        return @copy($source_file, $target_file);
    }

    /**
     * 递归删除文件和目录
     * @DateTime 2020-12-07
     * @param    array      $conds [
     *      ‘path_file’ =》 删除的目录
     *      ‘func’      => 删除条件。可以为空
     * ]
     * @return   [type]            [description]
     */
    public static function del_files(array $conds)
    {
        $path_file    = cls_arr::get($conds, 'path_file', '');
        $del_max_time = cls_arr::get($conds, 'del_max_time', 0);

        if (empty($path_file) || !file_exists($path_file))
        {
            return false;
        }

        if (!is_dir($path_file))
        {
            return unlink($path_file);
        }

        $fob = opendir($path_file);
        while ($file = readdir($fob))
        {
            if (!in_array($file, ['.', '..']))
            {
                $fullpath = "{$path_file}/{$file}";
                if (is_dir($fullpath))
                {
                    self::del_files([
                        'path_file'    => $fullpath,
                        'del_max_time' => $del_max_time
                    ]);
                    rmdir($fullpath);
                }
                else
                {
                    if (
                        $del_max_time <= 0 ||
                        ( $del_max_time > 0 && ( filectime($fullpath) - time() >= $del_max_time ))
                    )
                    {
                        unlink($fullpath);
                    }
                }
            }
        }
        closedir($fob);

        return true;
    }

    public static function scan_dir($dir, $filter = array())
    {
        if (!is_dir($dir)) return false;

        $files = array_diff(scandir($dir), array('.', '..'));
        if (is_array($files))
        {
            foreach ($files as $key => $value)
            {
                if (is_dir($path_dir = $dir . '/' . $value))
                {
                    $files[$value] = $return_data[$path_dir] = static::scan_dir($dir . '/' . $value, $filter);
                    unset($files[$key]);

                    if (empty($files[$value])) unset($files[$value]);
                    continue;
                }

                $pathinfo  = pathinfo($dir . '/' . $value);
                $extension = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';
                if (
                    (!empty($filter['extension']) && !in_array($extension, $filter['extension'])) ||
                    $value[0] == '.'
                )
                {
                    unset($files[$key]);
                }
            }
        }

        unset($key, $value);
        return $files;
    }

    // 遍历文件夹下的所有文件
    public static function list_file($dir, $dirpath = '', $filter = '')
    {
        if (!is_dir($dir)) return [];
        $files = array_diff(scandir($dir), array('.', '..', '.DS_Store'));
        $list  = [];
        if (is_array($files))
        {
            foreach ($files as $value)
            {
                if (is_dir($dir . '/' . $value))
                {
                    $tmp_list =  static::list_file($dir . '/' . $value, $dirpath .'/'. $value, $filter);

                    $list = array_merge($list, $tmp_list);
                    continue;
                }

                if (!empty($filter) && !strstr($value, $filter) )
                {
                    continue;
                }

                $list[] = $dirpath .'/'. $value;
            }
        }

        return $list;
    }

    /**
     * 递归创建目录
     *
     * @param  [type]  $path [description]
     * @param integer $mode [description]
     * @return [type]        [description]
     */
    public static function dir_create($path, $mode = 0777)
    {
        if (is_dir($path)) return true;
        $path    = self::dir_path($path);
        $temp    = explode('/', $path);
        $cur_dir = '';

        $max = count($temp) - 1;
        for ($i = 0; $i < $max; $i++)
        {
            $cur_dir .= $temp[$i];
            if (is_dir($cur_dir))
            {
                $cur_dir .= '/';
                continue;
            }

            if (@mkdir($cur_dir))
            {
                chmod($cur_dir, $mode);
            }

            $cur_dir .= '/';
        }
        return is_dir($path);
    }

    public static function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') $path = $path . '/';
        return $path;
    }
}
