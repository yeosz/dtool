<?php
/**
* Base
*
* User: 系统自动生成
* Date: 2017-09-29
* Time: 07:57
*/

namespace TableProvider;

use Yeosz\Dtool\Table;
use Yeosz\Dtool\DB;

class Base  extends Table
{
    protected $dbConfig = "TzoxNDoiWWVvc3pcRHRvb2xcREIiOjQ6e3M6MjA6IgBZZW9zelxEdG9vbFxEQgBob3N0IjtzOjE1OiJsb2NhbGhvc3Q6MzMwNjAiO3M6MjI6IgBZZW9zelxEdG9vbFxEQgBkYk5hbWUiO3M6OToiaG9tZXN0ZWFkIjtzOjIyOiIAWWVvc3pcRHRvb2xcREIAZGJVc2VyIjtzOjk6ImhvbWVzdGVhZCI7czoyNjoiAFllb3N6XER0b29sXERCAGRiUGFzc3dvcmQiO3M6Njoic2VjcmV0Ijt9";

    public function __construct(DB $db = null)
    {
        if (empty($db)) $db = unserialize(base64_decode($this->dbConfig));
        parent::__construct($db);
    }
}