<?php
use kali\core\lang;

/**
 * 获取语言包
 * @package Smarty
 * @subpackage plugins
 * <{lang key='' defaultvalue='' replace='replace value'}>
 * <{lang key='' defaultvalue='' replace=['cat', 10]}>
 */
function smarty_function_lang($params, &$smarty)
{
    if( empty($params['key']) )
    {
        return '';
    }
    else
    {
        $defaultvalue = empty($params['defaultvalue']) ? null : $params['defaultvalue'];
        $replace      = empty($params['replace']) ? array() : $params['replace'];
        $log_errors   = empty($params['log_errors']) ? true : $params['log_errors'];
        return lang::get($params['key'], $defaultvalue, $replace, $log_errors);
    }
}
