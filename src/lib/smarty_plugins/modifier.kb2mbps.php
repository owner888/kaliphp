<?php
/**
 * kb的流量转为Mbps
 *
 * @param $status
 * @return void
 * <{102400|kb2mbps}>
 */               
function smarty_modifier_kb2mbps( $wkb )
{
   return sprintf('%0.2f', $wkb * 8 / 1024);
}
