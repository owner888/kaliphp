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

/**
 * html转pdf类
 * 直接调用命令 wkhtmltopdf，使用时记得安全此命令，只能在cli环境下执行
 *
 * @version $Id$  
 */
class cls_wkhtmltopdf
{
    public static $binary = 'wkhtmltopdf';  // wkhtmltopdf、wkhtmltoimage
    public static $type = 'png';
    public static $command_options = array();
    public static $tmp_dir = '';
    public static $ignore_warnings = false;

    public static function generate($html, $filename = 'download.pdf', $exec = false, $download = false)
    {
        if($exec === true)
        {
            exec('xvfb-run --server-args="-screen 0, 1024x680x24" wkhtmltopdf --use-xserver "'.$html.'" '.$filename);
            $pdf = file_get_contents($filename);
        } 
        else 
        {
            $descriptorspec = array(
                0 => array('pipe', 'r'), // stdin
                1 => array('pipe', 'w'), // stdout
                2 => array('pipe', 'w'), // stderr
            );

            $process = proc_open('xvfb-run -a --server-args="-screen 0, 1024x768x24" wkhtmltopdf --use-xserver "'.$html.'" -', $descriptorspec, $pipes);

            $pdf = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            $return_value = proc_close($process);
            if ($errors) die('PDF GENERATOR ERROR:<br />' . nl2br(htmlspecialchars($errors)));
        }

        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Length: '.strlen($pdf));

        if($download === true)
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/force-download');
            header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
            header('Content-Type: application/pdf', false);
            header('Content-Disposition: attachment; filename="'.basename($filename).'";');
            header('Content-Transfer-Encoding: binary');
        } 
        else 
        {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="'.basename($filename).'";');
        }
        echo $pdf;
        die;
    }

    /**
     * @return string the mime type for the current image
     * @throws \Exception
     */
    public function get_mime_type()
    {
        if ($this->type === 'jpg') 
        {
            return 'image/jpeg';
        } 
        elseif ($this->type === 'png') 
        {
            return 'image/png';
        }
        elseif ($this->type === 'bmp') 
        {
            return 'image/bmp';
        }
        else 
        {
            throw new \Exception('Invalid image type');
        }
    }
}
