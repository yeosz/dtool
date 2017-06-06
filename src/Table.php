<?php

namespace Yeosz\Dtool;

/**
 * Class Table
 *
 * @property string table
 * @property string pk
 * @property Provider provider
 * @property array columns
 */
class Table
{
    /**
     * 供给器
     *
     * @var Provider
     */
    public $provider;

    /**
     * 表名
     *
     * @var string
     */
    public $table = '';

    /**
     * 主键
     *
     * @var string
     */
    public $pk = '';

    /**
     * 字段
     *
     * @var array
     */
    public $columns = [];

    /**
     * @var array
     */
    protected $current = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var DB
     */
    protected $db;

    /**
     * Table constructor.
     *
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
        $this->provider = new Provider();

        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (stripos($method, 'dataProvider') === 0) {
                $property = $this->provider->toUnderline(substr($method, 12));
                $property = ltrim(strtolower($property), '_');
                $this->methods[$property] = $method;
            }
        }
    }

    /**
     * 插入数据
     *
     * @param $count
     * @param \Closure $closure
     * @return array
     */
    public function create($count, \Closure $closure = null)
    {
        $row = [];
        for ($i = 0; $i < $count; $i++) {
            $data = $this->generate($closure);
            $result = $this->db->insert($this->table, $data, $this->pk);
            if ($this->pk) {
                $row[$result] = $data;
            } else {
                $row[] = $data;
            }
        }
        return $row;
    }

    /**
     * 生成数据
     *
     * @param \Closure $closure
     * @return array
     */
    public function generate(\Closure $closure = null)
    {
        $this->current = [];
        foreach ($this->columns as $key => $column) {
            if ($column instanceof \Closure) {
                $this->current[$key] = $column();
                continue;
            } elseif (is_array($column)) {
                $property = array_shift($column);
                if (isset($this->methods[$property])) {
                    $this->current[$key] = call_user_func_array([$this, $this->methods[$property]], $column);
                } elseif (method_exists($this->provider, $property)) {
                    $this->current[$key] = call_user_func_array([$this->provider, $property], $column);
                } elseif (method_exists($this->provider->numberProvider, $property)) {
                    $this->current[$key] = call_user_func_array([$this->provider->numberProvider, $property], $column);
                } elseif (method_exists($this->provider->datetimeProvider, $property)) {
                    $this->current[$key] = call_user_func_array([$this->provider->datetimeProvider, $property], $column);
                } else {
                    $this->current[$key] = $this->provider->$property;
                }
            } else {
                $this->current[$key] = $column;
            }
        }

        if ($closure) {
            $this->current = $closure($this->current);
        }

        return $this->current;
    }
}