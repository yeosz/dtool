<?php
/**
* dtool_test
*
* User: 系统自动生成
* Date: 2017-09-29
* Time: 07:57
*/

namespace TableProvider;

class DtoolTest  extends Base
{
    public $table = 'dtool_test';
    public $columns = [
        'username' => ["getString",16],
        'name' => ["getString",16],
        'sex' => ["randomValue",["2","1","0"]],
        'user_id' => ["int"],
        'price' => ["randomFloat",8,2],
        'address' => ["getString",16],
        'remarks' => ["getString",16],
        'created_at' => ["timestamp"],
    ];
    public $pk = 'id';
}