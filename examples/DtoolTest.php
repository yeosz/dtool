<?php
/**
* dtool_test
*
* User: 系统自动生成
* Date: 2017-06-06
* Time: 13:39
*/

use Yeosz\Dtool\Table;

Class DtoolTest extends Table
{
    public $table = 'dtool_test';
    public $columns = [
        'username' => ["getString",16],
        'name' => ["name",16],
        'sex' => ["randomValue",["2","1","0"]],
        'user_id' => ["user_id"],
        'price' => ["randomFloat",8,2],
        'address' => ["address"],
        'remarks' => ["getMbString",16],
        'created_at' => ["timestamp"],
    ];
    public $pk = 'id';

    /**
     * user_id字段的供应器
     *
     * @return int
     */
    protected function dataProviderUserId()
    {
        $ids = $this->db->column("select id from {$this->table}");
        if (empty($ids)) return 0;
        return $this->provider->randomValue($ids);
    }
}