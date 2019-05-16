<?php

use kali\core\req;

/**
 * 获取不确定的 request 元素（不存在时返回空，以防止出现变量未定义的警告）
 * @package Smarty
 * @subpackage plugins
 * <{request_em array= key= }>
 */
function smarty_function_request_em($params, &$smarty)
{
    if( !empty($params['array']) )
    {
        $arr = $params['array'];
        return isset($arr[$params['key']]) ? $arr[$params['key']] : '';
    }
    else
    {
        $default = empty($params['default']) ? '' : $params['default'];
        return req::item($params['key'], $default);
    }
}
