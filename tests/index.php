<?php

use kaliphp\kali;

define('APPPATH', __DIR__.'/app');
define('ENVPATH', __DIR__ .'/.env');

require_once __DIR__ . '/../vendor/autoload.php';

kali::registry();

kali::run();

