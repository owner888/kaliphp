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

class cls_aes
{
    private static $_instance;
    private static $blocksize = 16;

    private $_key = '';  // 密钥
    private $_iv = '';   // 向量

    // 单例模式
    public static function instance()
    {
        if (!self::$_instance instanceof self) 
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function set_key($key)
    {
        $this->_key = $key;
    }

    public function set_iv($iv)
    {
        $this->_iv = $iv;
    }

    /**
     * 加密
     */
    public function encrypt($value)
    {
        // openssl 需要补码
        $value = $this->pkcs7_pad($value);
        $value = openssl_encrypt($value, "aes-128-cbc", $this->_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->_iv);
        return $value;
    }

    /**
     * 解密
     */
    public function decrypt($value)
    {
        $value = openssl_decrypt($value, "aes-128-cbc", $this->_key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $this->_iv);
        $value = $this->pkcs7_unpad($value);
        return $value;
    }

    /**
     * pkcs7补码，CBC加密方式必须补码
     * 在PKCS5Padding中，明确定义Block的大小是8位
     * 而在PKCS7Padding定义中，对于块的大小是不确定的，可以在1-255之间（块长度超出255的尚待研究）
     * 填充值的算法都是一样的
     * @param string $string  明文
     * @return String
     */ 
    public function pkcs7_pad($str)
    {
        $len = strlen($str);
        if ($len % self::$blocksize != 0)
        {
            // 计算需要填充的位数
            $pad = self::$blocksize - ($len % self::$blocksize);
            // 获得补位所用的字符
            $str .= str_repeat(chr($pad), $pad);
        }
        return $str;
    }
 
    /**
     * 除去pkcs7补码
     * 
     * @param string 解密后的结果
     * @return string
     */
    public function pkcs7_unpad($str)
    {
        // 获得补位所用的字符，计算它的ASCII码，得到补码的长度
        $pad = ord(substr($str, -1));
        // 补码的长度超过或者等于补码块大小，说明明文是完整没有经过补码的
        if ($pad < 1 || $pad >= self::$blocksize)                                    
        {
            $pad = 0;
        }
        // 获得补位所用的字符，检查这个字符是否在这个区间出现的次数跟它的数值相等
        if( strspn($str, chr($pad), strlen($str) - $pad) != $pad )
        {
            $pad = 0;
        }
        // 去掉补码，返回数据
        return substr($str, 0, (strlen($str) - $pad));          
    }

}

/* vim: set expandtab: */

/*
注意：
用了OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING才能和mcrypt匹配得上，但是又需要补码，php的补码和其他语言不同，所以java解不开
不用OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING和mcrypt匹配不上，java也解不开，所以貌似openssl是没法用了

参考微信做法：
https://github.com/gaoming13/wechat-php-sdk/tree/master/src/WechatPhpSdk/Utils

js加密：
首先引入这几个js文件
下载地址http://pan.baidu.com/s/1sjNpESd
<script type="text/javascript" src="/CryptoJS/aes.js"></script>
<script type="text/javascript" src="/CryptoJS/pad-zeropadding.js"></script>
<script type="text/javascript">
var data="test";//加密字符串
var key  = CryptoJS.enc.Latin1.parse('@12345678912345!');//密钥
var iv   = CryptoJS.enc.Latin1.parse('@12345678912345!');//与密钥保持一致
//加密
var data = JSON.stringify(data);//将数据对象转换为json字符串
var encrypted = CryptoJS.AES.encrypt(data,key,{iv:iv,mode:CryptoJS.mode.CBC,padding:CryptoJS.pad.ZeroPadding});
encrypted=encodeURIComponent(encrypted);
document.write(decrypted);//输出加密后的字符串

//解密
var data="加密的字符串";
//key和iv和加密的时候一致
var decrypted = CryptoJS.AES.decrypt(data,key,{iv:iv,padding:CryptoJS.pad.ZeroPadding});
decrypted=decrypted.toString(CryptoJS.enc.Utf8);
document.write(decrypted);//输出解密后的数据
</script>
在实际用的时候和php传输中，js加密后的字符串里面的+被浏览器解析成了空格  然后php解密的时候出错；这里可以对加密之后的字符串做进一步处理encrypted=encodeURIComponent(encrypted);就没有这个问题了 * */
