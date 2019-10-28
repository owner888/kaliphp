<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */
/**
 * 1.如果有key,则取指定的key的值
 * 2.bind_name默认是KALI_PAGE_DATA，如果要设置为其他的加一个bind_name='xxx'
 * 调用方式：
 * 1.<{kali_page_data}>
 * 2.<{kali_page_data key='a'}> <{kali_page_data key=['a', 'n']}>
 * 3.自己指定bind_name
 * <{kali_page_data bind_name='fuck'}>
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link   http://www.smarty.net/manual/en/language.function.counter.php {counter}
 *         (Smarty online manual)
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @return string|null
 */
function smarty_function_kali_page_data($params = [], $template)
{
    static $tpl_vars = [];
    if ( !$tpl_vars )
    {
        foreach ($template->tpl_vars as $key => $smarty_val) 
        {
            $tpl_vars[$key] = $smarty_val->value;
        }
    }

    //如果有key,则取指定的key的值
    if( !empty($params['key']) )
    {
        $keys  = (array) $params['key'];
        foreach ($keys as $key) 
        {
            $value[$key] = isset($tpl_vars[$key]) ? $tpl_vars[$key] : null;
        }

        $value = json_encode($value);
    }
    //全部返回
    else
    {
        $value = json_encode($tpl_vars);   
    }


    //如果是有绑定名称/没有任何参数，会返回script包着的变量bind_name默认是KALI_PAGE_DATA
    $template_str  = '<script> var ';
    $template_str .= !empty($params['bind_name']) ? $params['bind_name'] : 'KALI_PAGE_DATA';
    $template_str .= " = {$value}; </script>";
 
    return $template_str;
}
