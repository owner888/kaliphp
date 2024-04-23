<?php
$configs = require APPPATH . '/../../common/config/'.basename(__FILE__);
// key为数字 不要用array_merge
// $configs[5003] = 'event class[%s] not exists';

return $configs;