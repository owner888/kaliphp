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
 * 使用异或（XOR）加密/解密文件
 * @version $Id$
 *
 * 例子：
 * $source = '111.jpg';
 * $encrypt_file = '111_enc.jpg';
 * $decrypt_file = '111_dec.jpg';
 * $key = 'D89475D32EA8BBE933DBD299599EEA3E';
 * cls_file_crypt::encrypt($source, $encrypt_file, $key);          // encrypt
 * cls_file_crypt::encrypt($encrypt_file, $decrypt_file, $key);    // decrypt
 */
class cls_file_crypt
{
    /** 文件加密,使用key与原文异或生成密文,解密则再执行一次异或即可
     * @param String $source 要加密或解密的文件
     * @param String $dest   加密或解密后的文件
     * @param String $key    密钥
     */
    public static function encrypt( $oldpath = null, $newpath = null, $key = null )
    {
        if ( file_exists($oldpath) )
        {
            $str = '';           // 处理后的字符串
            $len = strlen($key); // 密钥长度
            $index = 0;

            $pfr = fopen($oldpath, 'rb');
            $pfw = fopen($newpath, 'wb');

            while(!feof($pfr))
            {
                $tmp = fread($pfr, 1);
                $str = $tmp ^ substr($key,$index%$len,1);
                $index++;
                fwrite($pfw, $str);
            }

            fclose($pfr);
            fclose($pfw);
        }
        else
        {
            return false;
        }
    }
}



