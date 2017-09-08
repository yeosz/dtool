<?php

require_once '../vendor/autoload.php';

// Provider的使用
$provider = new \Yeosz\Dtool\Provider();
$data = [
    'string' => $provider->getString(10),
    'mb_string' => $provider->getMbString(10),
    'city' => $provider->city,
    'address' => $provider->address,
    'uuid' => $provider->uuid,
    'id_card' => $provider->id_card,
    'image_url' => $provider->image_url,
    'bitmap_url' => $provider->bitmap_url,
    'name' => $provider->name,
    'first_name' => $provider->first_name,
    'last_name' => $provider->last_name,
    'phone' => $provider->phone,
    'mobile' => $provider->mobile,
    'email' => $provider->email,
    'qq' => $provider->qq,
    'postcode' => $provider->postcode,
    'company_name' => $provider->company_name,
    'ean8' => $provider->ean8,
    'ean13' => $provider->ean13,
    'timestamp' => $provider->timestamp,
    'year' => $provider->year,
    'date' => $provider->date,
    'time' => $provider->time,
    'integer' => $provider->integer,
    'random' => $provider->randomValue([1, 2, 3]),
    'payment' => $provider->payment,
    'bank' => $provider->bank,
];
print_r($data);

$provider->addIncrement('sort', 1);
var_dump($provider->sort);
var_dump($provider->sort);
var_dump($provider->sort);

$provider->addProvider('my_time', function () {
    return time();
});
var_dump($provider->my_time);

// DB
$database = 'homestead';
$db = new Yeosz\Dtool\DB('localhost:33060', 'homestead', 'homestead', 'secret');

$sql = "DROP TABLE IF EXISTS `dtool_test`";
$db->query($sql);
$sql = "CREATE TABLE `dtool_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `sex` enum('2','1','0') NOT NULL COMMENT '性别:1男2女0未知',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'user id',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `address` varchar(60) NOT NULL DEFAULT '' COMMENT '地址',
  `remarks` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";
$db->query($sql);


$tool = new Yeosz\Dtool\MysqlTool($db, $database);
// 生成文档
file_put_contents($database . '.html', $tool->getDocument());
// 生成TableProvider
$tool->buildTableProvider('./', '');

// TableProvider的使用
include './DtoolTest.php';
$table = new DtoolTest($db);
$data = $table->generate();
$db->insert('dtool_test', $data);
$table->create(2);

// sql查询
$row = $db->query("select id,name from dtool_test where id=:id", ['id' => 1]);
print_r($row);

$column = $db->column("select name from dtool_test");
print_r($column);

$cell = $db->cell("select name from dtool_test where id=?", [1]);
print_r($cell);

