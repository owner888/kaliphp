<?php
/**
 * 状态转提示文字标识
 *
 * @param $status
 * @return void
 */               
function smarty_modifier_status_img( $status, $m='text' )
{
    $server_status = config::instance('config')->get('server_status');
    if( isset($server_status[$status]) )
    {
        if( $status != 1 ) 
        {
            return "<span style='color:red'>".$server_status[$status]."</span>";
        } 
        else 
        {
            return $server_status[$status];
        }
    } 
    else 
    {
        return '未知';
    }
}
