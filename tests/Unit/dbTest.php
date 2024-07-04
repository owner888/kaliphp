<?php
use kaliphp\db;
use kaliphp\util;
use kaliphp\kali;
use kaliphp\config;

kali::registry();
$test_table = $_ENV['DB_PREFIX'].'_pset_test01';
//设置json_fields方便测试
$json_fields = config::instance('database')->get('json_fields', []);
config::instance('database')->set('json_fields',array_merge($json_fields,[
    $test_table => ['json_data']
]));

// 测试 db.php
it('create_table', function () use($test_table) {	
	$create_table_sql = "CREATE TABLE `{$test_table}` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内容表',
	  `name` varchar(50) DEFAULT NULL COMMENT '名称',
	  `json_data` text COMMENT '内容',
	  `status` tinyint(4) DEFAULT NULL COMMENT '状态 0=禁用 1=启用',
	  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
	  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
	  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
	  PRIMARY KEY (`id`),
	  KEY `name` (`name`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
	$result = db::query($create_table_sql)->execute();
	expect($result)->toBeArray();
});

it('CURD', function () use($test_table) {	

	$json_data = [
		'zh-cn' => '测试001',
		'en' => 'test001',
	];
	[,$result] = db::insert($test_table)->set([
		'name' => 'test01',
		'json_data' => $json_data
	])->execute();
	expect($result)->toBe(1);

	$result = db::select('*')
		->from($test_table)
		->where(['name' => 'test01'])
		->as_row()->limit(1)->execute();
	expect($result)->toBeArray();

	$result_json_data = json_decode($result['json_data'], true);
	expect($result_json_data)->toBe($json_data);

	$json_data['zh-cn'] = '测试002';
	$result = db::update($test_table)
		->set([
			'json_data' => [
				'zh-cn' => $json_data['zh-cn']
			]
		])
		->where(['name' => 'test01'])
		->execute();
	expect($result)->toBe(1);

	$result = db::select('*')
		->from($test_table)
		->where(['name' => 'test01'])
		->as_row()->limit(1)->execute();
	expect($result)->toBeArray();
	$result_json_data = json_decode($result['json_data'], true);
	expect($result_json_data['zh-cn'])->toBe($json_data['zh-cn']);
	expect($result_json_data['en'])->toBe($json_data['en']);

});

it('drop_table', function () use($test_table) {	
	$drop_table_sql = "DROP TABLE `{$test_table}`";
	$result = db::query($drop_table_sql)->execute();
	expect($result)->toBeArray();
});


