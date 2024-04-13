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
 * RSA操作类
 * 用openssl生成rsa密钥对(私钥/公钥):
   openssl genrsa -out rsa_private_key.pem 2048
   openssl rsa -pubout -in rsa_private_key.pem -out rsa_public_key.pem
 *
 * @version $Id$  
 */
class cls_rsa
{
    public static function encrypt($plaintext, $rsa_public_key_path)
    {
        openssl_public_encrypt($plaintext, $encrypted, $rsa_public_key_path);
        $encrypted = base64_encode($encrypted);
        return $encrypted;
        //openssl_public_encrypt($data, $encrypted, file_get_contents(dirname(__FILE__).'/rsa_public_key.pem'));
        //echo '公钥加密: '.base64_encode($encrypted)."\n";
    }

    public static function decrypt($ciphertext, $rsa_private_key_path)
    {
        $encrypted = base64_decode($ciphertext);
        openssl_private_decrypt($ciphertext, $decrypted, $rsa_private_key_path);
        var_dump($decrypted);
        return $decrypted;
        //echo '私钥解密: '.$decrypted."\n";
    }
}
