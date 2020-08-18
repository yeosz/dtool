<?php

namespace Yeosz\Dtool;

class MysqlTool
{
    /**
     * @var DB
     */
    protected $db;

    /**
     * 数据库
     * @var string
     */
    protected $database;

    /**
     * 排除的表
     * @var array
     */
    protected $exceptTable = [];

    /**
     * 默认注释
     * @var array
     */
    protected $defaultComment = [];

    /**
     * Tool constructor.
     *
     * @param DB $db
     * @param string $database
     */
    public function __construct(DB $db, $database)
    {
        $this->database = $database;
        $this->db = $db;
        $this->db->query('set sql_mode ="strict_trans_tables,no_zero_in_date,no_zero_date,error_for_division_by_zero,no_engine_substitution";');
    }

    /**
     * 获取所有表及表字段
     *
     * @return array
     */
    public function getTable()
    {
        $field = [
            'table_name',
            'table_comment',
            'table_type',
            'engine',
        ];
        $sql = 'select ' . implode($field, ',') . ' from information_schema.tables where table_schema="' . $this->database . '"';
        $result = $this->db->query($sql);

        $result = $this->keyToLower($result);
        $tables = [];
        foreach ($result as $item) {
            if (!in_array($item['table_name'], $this->exceptTable)) {
                $tables[] = $item;
            }
        }

        // 表字段
        foreach ($tables as $key => $value) {
            $sql = "select 
                    table_schema,table_name,column_name,extra,column_comment,data_type,column_type,column_default,is_nullable,column_key
                    from information_schema.columns
                    where table_name = '{$value['table_name']}' and table_schema = '{$this->database}'";
            $result = $this->db->query($sql);
            $tables[$key]['column'] = $this->keyToLower($result);
        }
        return $tables;
    }

    /**
     * 获取所有主键
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        $sql = "select table_schema,table_name,column_name
            from information_schema.key_column_usage 
            where constraint_name='primary' and table_schema='{$this->database}'";
        $result = $this->db->query($sql);
        $result = $this->keyToLower($result);
        $primary = [];
        foreach ($result as $item) {
            $primary[] = $item['table_schema'] . '.' . $item['table_name'] . '.' . $item['column_name'];
        }
        return $primary;
    }

    /**
     * 获取所有外键
     *
     * @return array
     */
    public function getForeignKey()
    {
        $sql = "select concat(table_name, '.', column_name) as foreign_key,
            referenced_table_schema as db,
            concat(referenced_table_name, '.', referenced_column_name) as field
            from information_schema.key_column_usage
            where table_schema = '{$this->database}' and referenced_table_name is not null";
        $result = $this->db->query($sql);
        $result = $this->keyToLower($result);
        $foreignKey = [];
        foreach ($result as $item) {
            $foreignKey[$item['foreign_key']] = $item['db'] == $this->database ? $item['field'] : ($item['db'] . '.' . $item['field']);
        }
        return $foreignKey;
    }

    /**
     * 获取所有触发器
     *
     * @return array
     */
    public function getTrigger()
    {
        $sql = "show triggers";
        $result = $this->db->query($sql);
        $result = $this->keyToLower($result);
        $trigger = [];
        foreach ($result as $item) {
            $trigger[$item['table']][] = [
                'name' => $item['trigger'],
                'event' => $item['event'],
                'statement' => $item['statement'],
                'timing' => $item['timing']
            ];
        }
        return $trigger;
    }

    /**
     * 获取所有非主键索引
     *
     * @return array
     */
    public function getIndex()
    {
        $sql = "select table_name,index_name,column_name,index_type,non_unique 
                from information_schema.statistics 
                where index_schema = '{$this->database}' and index_name <> 'primary' 
                order by index_name asc, seq_in_index asc";
        $result = $this->db->query($sql);
        $result = $this->keyToLower($result);

        $map = [];
        foreach ($result as $key => $item) {
            if (!isset($map[$item['index_name']])) {
                $map[$item['index_name']] = $key;
            } else {
                $id = $map[$item['index_name']];
                $result[$id]['column_name'] = $result[$id]['column_name'] . ',' . $item['column_name'];
                unset($result[$key]);
            }
        }

        $index = array();
        foreach ($result as $item) {
            if (!isset($index[$item['table_name']])) $index[$item['table_name']] = [];
            $index[$item['table_name']][] = $item;
        }


        return $index;
    }

    /**
     * 获取文档内容
     *
     * @param array $columnDefaultComment
     * @param array $exceptTable
     * @return string
     */
    public function getDocument($columnDefaultComment = [], $exceptTable = [])
    {
        $this->exceptTable = $exceptTable;
        $this->defaultComment = $columnDefaultComment;

        ob_start();
        include __DIR__ . '/resources/dict.php';
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }


    /**
     * 生成表供应器
     *
     * @param string $path
     * @param string $namespace
     * @param array $tables
     */
    public function buildTableProvider($path, $namespace, $tables = [])
    {
        if (!is_dir($path)) {
            mkdir($path, true);
        }

        $baseClass = [
            '<?php',
            '/**',
            ' * Base',
            ' *',
            ' * User: ' . '系统自动生成',
            ' * Date: ' . date('Y-m-d'),
            ' * Time: ' . date('H:i'),
            ' */',
            '',
            $namespace ? "namespace {$namespace};" : '',
            '',
            'use ' . __NAMESPACE__ . '\Table;',
            'use ' . __NAMESPACE__ . '\DB;',
            '',
            "class Base extends Table",
            '{',
            '    protected $dbConfig = "' . base64_encode(serialize($this->db)) . '";',
            '',
            '    public function __construct(DB $db = null)',
            '    {',
            '        if (empty($db)) $db = unserialize(base64_decode($this->dbConfig));',
            '        parent::__construct($db);',
            '    }',
            '}',
        ];
        file_put_contents($path . '/Base.php', implode("\r\n", $baseClass));

        $tableInfo = $this->getTable();
        $pks = $this->getPrimaryKey();
        foreach ($tableInfo as $table) {
            if (!empty($tables) && !in_array($table['table_name'], $tables)) {
                continue;
            }
            $pkStr = "    public \$pk = '';";
            $class = [
                '<?php',
                '/**',
                '* ' . $table['table_name'],
                '*',
                '* User: ' . '系统自动生成',
                '* Date: ' . date('Y-m-d'),
                '* Time: ' . date('H:i'),
                '*/',
                '',
                $namespace ? "namespace {$namespace};" : '',
                '',
            ];
            $fileName = Provider::toHump($table['table_name'], true);
            $class[] = "class {$fileName}  extends Base";
            $class[] = "{";
            $class[] = "    public \$table = '{$table['table_name']}';";

            $class[] = "    public \$columns = [";
            foreach ($table['column'] as $column) {
                $pk = $column['table_schema'] . '.' . $table['table_name'] . '.' . $column['column_name'];
                if (in_array($pk, $pks) && $column['extra'] == 'auto_increment') {
                    $pkStr = "    public \$pk = '{$column['column_name']}';";;
                    continue;
                } else {
                    $class[] = "        '{$column['column_name']}' => " . $this->transform($column['data_type'], $column['column_type']) . ",";
                }
            }
            $class[] = '    ];';
            $class[] = $pkStr;
            $class[] = '}';
            $string = implode("\r\n", $class);
            file_put_contents($path . '/' . $fileName . '.php', $string);
        }
    }

    /**
     * 根据数据库字段类型转换
     *
     * @param $dataType
     * @param $columnType
     * @return int|string
     */
    private function transform($dataType, $columnType)
    {
        $getInfo = function () use ($columnType) {
            $search = array_merge(range('a', 'z'), ['(', ')', "'"]);
            $columnType = str_replace($search, '', $columnType);
            return is_numeric($columnType) ? $columnType : explode(',', $columnType);
        };
        $getResult = function () {
            $args = func_get_args();
            return json_encode($args, JSON_UNESCAPED_UNICODE);
        };
        switch ($dataType) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'bigint':
            case 'integer':
            case 'int':
                $result = $getResult($dataType);
                break;
            case 'float':
            case 'double':
            case 'decimal':
                $info = $getInfo();
                $info[1] = empty($info[1]) ? 2 : intval($info[1]);
                $info[0] = empty($info[0]) ? 10 : ($info[0] - $info[1]);
                $result = $getResult('randomFloat', $info[0], $info[1]);
                break;
            case 'char':
                $result = $getResult('getString', $getInfo());
                break;
            case 'varchar':
                $length = $getInfo();
                $length = $length > 16 ? 16 : intval($length);
                $result = $getResult('getString', $length);
                break;
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                $result = $getResult('getString', 32);
                break;
            case 'date':
            case 'time':
            case 'year':
            case 'datetime':
            case 'timestamp':
                $result = $getResult($dataType);
                break;
            case 'set':
            case 'enum':
                $result = $getResult('randomValue', $getInfo());
                break;
            case 'json':
                $result = "'{}'";
                break;
            default:
                $result = "null";
        }
        return $result;
    }

    /**
     * KEY转小写
     *
     * @param $arr
     * @return array
     */
    protected function keyToLower($arr)
    {
        $result = [];
        foreach ($arr as $key => $value) {
            $key = is_numeric($key) ? $key : strtolower($key);
            $value = is_array($value) ? $this->keyToLower($value) : $value;
            $result[$key] = $value;
        }
        return $result;
    }
}