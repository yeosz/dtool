<?php

namespace Yeosz\Dtool;

class MysqlCompare
{
    /**
     * @var DB
     */
    protected $target;

    /**
     * @var DB
     */
    protected $source;

    /**
     * MysqlCompare constructor.
     * @param DB $target
     * @param DB $source
     */
    public function __construct(DB $target, DB $source)
    {
        $this->target = $target;
        $this->source = $source;
    }

    /**
     * 返回数据库比对
     *
     * @descrption
     * drop_table 要删除的表
     * create_table 要创建的表
     * alter_table_add 表需要新增的字段或索引
     * alter_table_modify 表需要修改的字段或索引
     * alter_table_drop 表需要删除的字段或索引
     *
     * @return array
     */
    public function showDiff()
    {
        $sourceInfo = $this->getDbInfo($this->source);
        $targetInfo = $this->getDbInfo($this->target);
        return $this->getDbDiff($targetInfo[0], $sourceInfo[0]);
    }

    /**
     * 结构同步的SQL 仅代参考
     * 程序无法识别字段名或索引名称的修改,请人工甄别,并注意SQL执行顺序
     *
     * @return array
     */
    public function showSql()
    {
        $sourceInfo = $this->getDbInfo($this->source);
        $targetInfo = $this->getDbInfo($this->target);
        $diff = $this->getDbDiff($targetInfo[0], $sourceInfo[0]);
        $result = $this->getSyncSql($diff, $sourceInfo[0], $sourceInfo[1]);
        return $result;
    }

    /**
     * 生成同步的sql
     * 程序无法识别字段名或索引名称的修改,请人工甄别,并注意SQL执行顺序
     *
     * @param $diff
     * @param $createInfo
     * @param $creates
     * @return array
     */
    protected function getSyncSql($diff, $createInfo, $creates)
    {
        $result = [];
        if (!empty($diff['drop_table'])) {
            foreach ($diff['drop_table'] as $table) {
                $result[] = "DROP TABLE IF EXISTS `{$table}`";
            }
            unset($diff['drop_table']);
        }
        if (!empty($diff['create_table'])) {
            foreach ($diff['create_table'] as $table) {
                $result[] = $creates[$table];
            }
            unset($diff['create_table']);
        }
        $getType = function ($str) {
            $start1 = substr($str, 0, 1);
            $start2 = substr($str, 0, 4);
            if ($start1 == '`') {
                return 'COLUMN';
            } elseif ($start2 == 'KEY ') {
                return 'KEY';
            } elseif ($start2 == 'UNIQ') {
                return 'UNIQUE_KEY';
            } elseif ($start2 == 'PRIM') {
                return 'PRIMARY_KEY';
            } else {
                throw new \Exception('未知类型');
            }
        };
        $getSql = function ($action, $type, $table, $key, $suggest) {
            if ($action == 'alter_table_drop') {
                return $this->alertTableDrop($table, $type, $key);
            } elseif ($action == 'alter_table_add') {
                return $this->alertTableAdd($table, $type, $suggest);
            } elseif ($action == 'alter_table_modify') {
                return $this->alertTableModify($table, $type, $key, $suggest);
            } else {
                throw new \Exception('未知类型');
            }
        };
        foreach ($diff as $table => $info) {
            foreach ($info as $key => $item) {
                foreach ($item as $value) {
                    $type = $getType($value);
                    $suggest = $createInfo[$table][$value] ?? '';
                    $sql = $getSql($key, $type, $table, $value, $suggest);
                    $result[$table][] = $sql;
                }
            }
        }
        return $result;
    }

    /**
     *
     *
     * @param $target
     * @param $source
     * @return array
     */
    protected function getDbDiff($target, $source)
    {
        $diff = [
            'drop_table' => [],
            'create_table' => [],
        ];
        foreach ($target as $key => $value) {
            if (isset($source[$key])) {
                $diff1 = array_diff($value, $source[$key]);
                foreach ($diff1 as $k => $item) {
                    if (isset($source[$key][$k])) {
                        $diff[$key]['alter_table_modify'][] = $k;
                    } else {
                        $diff[$key]['alter_table_drop'][] = $k;
                    }
                }
                $diff2 = array_diff($source[$key], $value);
                foreach ($diff2 as $k => $item) {
                    if (!isset($target[$key][$k])) {
                        $diff[$key]['alter_table_add'][] = $k;
                    }
                }
            } else {
                $diff['drop_table'][] = $key;
            }
        }
        foreach ($source as $key => $value) {
            if (!isset($target[$key])) {
                $diff['create_table'][] = $key;
            }
        }
        return $diff;
    }

    /**
     * 获取数据库的表信息
     *
     * @param $db
     * @return array
     */
    protected function getDbInfo($db)
    {
        $tables = $db->column('show full tables where TABLE_TYPE="BASE TABLE"');
        $strpos = function ($str) {
            if (substr($str, 0, 11) == 'PRIMARY KEY') return $str;
            $pos1 = strpos($str, '`');
            $pos2 = strpos($str, '`', $pos1 + 1);
            $pos2 = $pos2 + 1;
            $key = substr($str, 0, $pos2);
            return $key;
        };

        $info = [];
        $creates = [];
        foreach ($tables as $table) {
            $create = $db->row("show create table {$table}");
            $create = end($create);
            $creates[$table] = $create;
            $rows = explode("\n", $create);
            $length = count($rows) - 2;
            $rows = array_slice($rows, 1, $length);
            $rows = array_map('trim', $rows);

            $temp = [];
            foreach ($rows as $row) {
                $row = rtrim($row, ',');
                $temp[$strpos($row)] = $row;
            }
            $info[$table] = $temp;
        }
        return [$info, $creates];
    }

    /**
     * 生成删除的SQL
     *
     * @param $table
     * @param $type
     * @param $key
     * @return string
     */
    protected function alertTableDrop($table, $type, $key)
    {
        switch ($type) {
            case 'COLUMN':
                return sprintf('ALTER TABLE `%s` DROP COLUMN %s', $table, $key);
            case 'KEY':
                $key = substr($key, 4);
                return sprintf('ALTER TABLE `%s` DROP INDEX %s', $table, $key);
            case 'UNIQUE_KEY':
                $key = substr($key, 10);
                return sprintf('ALTER TABLE `%s` DROP INDEX %s', $table, $key);
            case 'PRIMARY_KEY':
                return sprintf('ALTER TABLE `%s` DROP PRIMARY KEY', $table);
        }
        return '';
    }

    /**
     * 生成新增的SQL
     *
     * @param $table
     * @param $type
     * @param $suggest
     * @return string
     */
    protected function alertTableAdd($table, $type, $suggest)
    {
        switch ($type) {
            case 'COLUMN':
                return sprintf('ALTER TABLE `%s` ADD COLUMN %s', $table, $suggest);
            case 'KEY':
            case 'UNIQUE_KEY':
                $suggest = str_replace(' KEY ', ' INDEX ', $suggest);
                return sprintf('ALTER TABLE `%s` ADD %s', $table, $suggest);
            case 'PRIMARY_KEY':
                return sprintf('ALTER TABLE `%s` ADD %s', $table, $suggest);
        }
        return '';
    }

    /**
     * 生成修改的SQL
     *
     * @param $table
     * @param $type
     * @param $key
     * @param $suggest
     * @return string
     */
    protected function alertTableModify($table, $type, $key, $suggest)
    {
        switch ($type) {
            case 'COLUMN':
                return sprintf('ALTER TABLE `%s` MODIFY COLUMN %s', $table, $suggest);
            case 'KEY':
                $key = substr($key, 4);
                $suggest = str_replace(' KEY ', ' INDEX ', $suggest);
                return sprintf('ALTER TABLE `%s` DROP INDEX %s,ADD %s', $table, $key, $suggest);
            case 'UNIQUE_KEY':
                $key = substr($key, 10);
                $suggest = str_replace(' KEY ', ' INDEX ', $suggest);
                return sprintf('ALTER TABLE `%s` DROP INDEX %s,ADD %s', $table, $key, $suggest);
            case 'PRIMARY_KEY':
                return sprintf('ALTER TABLE `%s` DROP PRIMARY KEY,ADD %s', $table, $suggest);
        }
        return '';
    }
}
