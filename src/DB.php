<?php

namespace Yeosz\Dtool;

class DB
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $dbUser;

    /**
     * @var string
     */
    private $dbPassword;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $sQuery;

    /**
     * @var bool
     */
    private $bConnected = false;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var int
     */
    public $rowCount = 0;

    /**
     * @var int
     */
    public $columnCount = 0;

    /**
     * @var int
     */
    public $queryCount = 0;

    /**
     * DB constructor.
     *
     * @param $host
     * @param $dbName
     * @param $dbUser
     * @param $dbPassword
     */
    public function __construct($host, $dbName, $dbUser, $dbPassword)
    {
        $this->host = $host;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->connect();
    }

    /**
     * 创建连接
     */
    private function connect()
    {
        try {
            $this->pdo = new \PDO('mysql:dbname=' . $this->dbName . ';host=' . $this->host . ';charset=utf8',
                $this->dbUser,
                $this->dbPassword,
                array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_PERSISTENT => true, // 长链接
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                )
            );
            $this->bConnected = true;
        } catch (\PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            die();
        }
    }

    /**
     * 关闭连接
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

    /**
     * 转化
     *
     * @param $query
     * @param array $parameters
     */
    private function init($query, $parameters = [])
    {
        if (!$this->bConnected) {
            $this->connect();
        }
        $this->parameters = $parameters;

        // 重连再试
        try {
            $this->sQuery = @$this->pdo->prepare($this->buildParams($query, $this->parameters));
        } catch (\PDOException $e) {
            if (strtolower($e->getCode()) == 'hy000') {
                $this->connect();
                $this->sQuery = $this->pdo->prepare($this->buildParams($query, $this->parameters));
            }
        }

        if (!empty($this->parameters)) {
            if (array_key_exists(0, $parameters)) {
                $parametersType = true;
                array_unshift($this->parameters, '');
                unset($this->parameters[0]);
            } else {
                $parametersType = false;
            }
            foreach ($this->parameters as $column => $value) {
                $this->sQuery->bindParam($parametersType ? intval($column) : ':' . $column, $this->parameters[$column]);
            }
        }

        $this->sQuery->execute();
        $this->queryCount++;

        $this->parameters = array();
    }

    /**
     * 拼接参数
     *
     * @param $query
     * @param null $params
     * @return mixed
     */
    private function buildParams($query, $params = null)
    {
        if (!empty($params)) {
            $rawStatement = explode(" ", $query);
            foreach ($rawStatement as $value) {
                if (strtolower($value) == 'in') {
                    return str_replace("(?)", "(" . implode(",", array_fill(0, count($params), "?")) . ")", $query);
                }
            }
        }
        return $query;
    }

    /**
     * 执行sql
     *
     * @param $query
     * @param null $params
     * @param int $fetchMode
     * @return null
     */
    public function query($query, $params = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $query = trim($query);
        $rawStatement = explode(" ", $query);
        $this->init($query, $params);
        $statement = strtolower($rawStatement[0]);
        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchMode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }

    /**
     * 插入数据
     *
     * @param $table
     * @param $insertRow
     * @param int|string|bool $returnPk
     * @param bool $ignore
     * @return bool|int
     */
    public function insert($table, $insertRow, $returnPk = 0, $ignore = false)
    {
        $sql = [[], []];
        foreach ($insertRow as $key => $v) {
            $sql[0][] = $key;
            $sql[1][] = ':' . $key;
        }
        $sql[0] = implode(',', $sql[0]);
        $sql[1] = implode(',', $sql[1]);

        $ignoreStr = $ignore ? 'ignore' : '';
        $result = $this->query("INSERT {$ignoreStr} INTO {$table} ({$sql[0]}) VALUES ($sql[1])", $insertRow);
        return empty($returnPk) ? $result : $this->lastInsertId();
    }

    /**
     * 新增的id
     *
     * @return mixed
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 获取一列数据
     *
     * @param $query
     * @param null $params
     * @return mixed
     */
    public function column($query, $params = null)
    {
        $this->init($query, $params);
        $resultColumn = $this->sQuery->fetchAll(\PDO::FETCH_COLUMN);
        $this->rowCount = $this->sQuery->rowCount();
        $this->columnCount = $this->sQuery->columnCount();
        $this->sQuery->closeCursor();
        return $resultColumn;
    }

    /**
     * 获取一行数据
     *
     * @param $query
     * @param null $params
     * @param int $fetchMode
     * @return mixed
     */
    public function row($query, $params = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $this->init($query, $params);
        $resultRow = $this->sQuery->fetch($fetchMode);
        $this->rowCount = $this->sQuery->rowCount();
        $this->columnCount = $this->sQuery->columnCount();
        $this->sQuery->closeCursor();
        return $resultRow;
    }

    /**
     * 获取单元格数据
     *
     * @param $query
     * @param null $params
     * @return mixed
     */
    public function cell($query, $params = null)
    {
        $this->init($query, $params);
        return $this->sQuery->fetchColumn();
    }

    /**
     * 拼接WHERE in条件
     *
     * @param string $field 字段
     * @param string|array $condition 条件
     * @param bool $isNumeric 是否数值,默认 true
     * @return string
     */
    public function buildInCondition($field, $condition, $isNumeric = true)
    {
        if (is_string($condition)) {
            $condition = explode(',', $condition);
        }
        if (empty($condition)) {
            return "1=2";
        }
        if ($isNumeric) {
            $condition = array_map('floatval', $condition);
            $condition = array_unique($condition);
            return count($condition) == 1 ? "{$field}={$condition[0]}" : "{$field} in (" . implode(',', $condition) . ")";
        } else {
            $condition = array_map('trim', $condition);
            $condition = array_unique($condition);
            return count($condition) == 1 ? "{$field}='{$condition[0]}'" : "{$field} in ('" . implode("','", $condition) . "')";
        }
    }
}