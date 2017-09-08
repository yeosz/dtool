<?php

require_once '../vendor/autoload.php';

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

$provider->addIncrement('sort', 1);
var_dump($provider->sort);
var_dump($provider->sort);
var_dump($provider->sort);

$provider->addProvider('my_time', function(){
    return time();
});
var_dump($provider->my_time);

die;

$db = new Yeosz\Dtool\DB('localhost:33060', 'homestead', 'homestead', 'secret');


$sql = "DROP TABLE IF EXISTS `dtool_test`";
$db->query($sql);

$sql = "CREATE TABLE `dtool_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(20) NOT NULL DEFAULT '',
  `sex` enum('2','1','0') NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `address` varchar(60) NOT NULL DEFAULT '',
  `remarks` varchar(128) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
$db->query($sql);

include './DtoolTest.php';

$table = new DtoolTest($db);
$data = $table->generate();
$db->insert('dtool_test', $data);
$table->create(2);

$row = $db->query("select id,name from dtool_test where id=:id", ['id'=>1]);
print_r($row);

$column = $db->column("select name from dtool_test");
print_r($column);

$cell = $db->cell("select name from dtool_test where id=?", [1]);
print_r($cell);

die;

//$tool = new Yeosz\Dtool\MysqlTool($db, 'dtool');
//echo $tool->getDocument();
//$tool->buildTableProvider('./', 'Table');




