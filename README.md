dtool
======

## 安装

使用 Composer 安装:

```
composer require "yeosz/dtool"
```

## 使用

### Provider 数据供给器

```php
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
    'color_name' => $provider->color_name,
    'color_hex' => $provider->color_hex,
    'color_rgb' => $provider->color_rgb,
    'version' => $provider->version,
    'between' => $provider->between(11, 99, 1000),
];

print_r($data);

// 数字供应器 numberProvider
$provider->numberProvider->randomMediumint();
// 时间供应器 datetimeProvider
$provider->datetimeProvider->time();

// 自增长
$provider->addIncrement('sort', 1);
var_dump($provider->sort);
var_dump($provider->sort);
var_dump($provider->sort);

// 自定义供应器
$provider->addProvider('my_time', function(){
    return time();
});
var_dump($provider->my_time);
```

### DB PDO封装

```php

$db = new Yeosz\Dtool\DB('localhost;port=33060', 'homestead', 'homestead', 'secret');

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

$rows = $db->query("select * from dtool_test where " . $db->buildInCondition('id', [1,2,3,4], true) . " order by id desc");
print_r($rows);

$row = $db->row("select id,name from dtool_test where id=:id", ['id'=>1]);
print_r($row);

$column = $db->column("select name from dtool_test");
print_r($column);

$cell = $db->cell("select name from dtool_test where id=?", [1]);
print_r($cell);
```

### MysqlTool MySQL工具

```php
$db = new Yeosz\Dtool\DB('localhost:33060', 'homestead', 'homestead', 'secret');

$tool = new Yeosz\Dtool\MysqlTool($db, 'homestead');
// 生成文档
file_put_contents('./document.html', $tool->getDocument());

// 生成表数据供应器
$tool->buildTableProvider('./tp/', 'TableProvider');
```

![image](https://raw.githubusercontent.com/yeosz/dtool/master/examples/doc.png)

### TableProvider 表数据供给器

- [DtoolTest](https://github.com/yeosz/dtool/blob/master/examples/tp/DtoolTest.php)

```php
// omposer.json修改autoload部分,增加命名空间
$table = new \TableProvider\DtoolTest();
$data = $table->generate(); // 生成数据,但不插入数据库
$table->db->insert('dtool_test', $data);
$table->create(2); // 生成数据,并插入数据库
// generate和create都可以传入一个闭包参数,对数据进行修改
$data = $table->generate(function ($current){
    $current['user_id'] = mt_rand(111,999);
    return $current;
});

// 表也可以针对某列自定义数据供应器 方法名以dataProvider开头
class DtoolTest  extends Base
{
    public $table = 'dtool_test';
    public $columns = [
        'username' => ["getString",16],
        'name' => ["getString",16],
        'sex' => ["randomValue",["2","1","0"]],
        'user_id' => ["custom_user_id"],
        'price' => ["randomFloat",8,2],
        'address' => ["getString",16],
        'remarks' => ["getString",16],
        'created_at' => ["timestamp"],
    ];
    public $pk = 'id';
    
    public function dataProviderCustomUserId()
    {
        return mt_rand(111,999);
    }
}

```

### MysqlCompare 结构同步

```php

$db1 = new Yeosz\Dtool\DB('localhost', 'demo1', 'homestead', 'secret');
$db2 = new Yeosz\Dtool\DB('localhost', 'demo2', 'homestead', 'secret');

$diff = new \Yeosz\Dtool\MysqlCompare($db2, $db1);
$diff->showSql();

```

### Postman

- [postman.js](https://github.com/yeosz/dtool/blob/master/src/resources/postman.js)

接口调试时生成随机数据，方便测试（建议先压缩）

![image](https://raw.githubusercontent.com/yeosz/dtool/master/examples/postman.png)

## 参考

- [详细参考资料](https://github.com/yeosz/dtool/tree/master/src/resources)

# License

MIT
