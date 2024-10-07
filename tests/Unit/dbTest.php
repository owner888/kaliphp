<?php

use kaliphp\db;
use kaliphp\kali;

kali::registry();

$table = 'test';

// SELECT
it('select', function () use($table) {	
	$sql = db::select()->from($table)->sql();
	expect($sql)->toBeString('SELECT * FROM `test`');
});

