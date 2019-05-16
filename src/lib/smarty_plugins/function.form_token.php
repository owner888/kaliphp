<?php

use kali\core\config;
use kali\core\lib\cls_security;

/**
 * 获取不确定的 request 元素（不存在时返回空，以防止出现变量未定义的警告）
 * @package Smarty
 * @subpackage plugins
 * <{form_token type="form"}>
 */
function smarty_function_form_token($params, &$smarty)
{
    $config = config::instance('config')->get('request');
    if ( $config['csrf_token_on'] ) 
    {
        $type = empty($params['type']) ? "form" : $params['type'];

        $token = cls_security::get_csrf_hash();
        
        if ($type == 'form') 
        {
            return '<input type="hidden" name="'.cls_security::get_csrf_token_name().'" value="'.$token.'" />';
        }
        else 
        {
            return $token;
        }
    }
    else 
    {
        return '';
    }
}
