<?php

namespace Framework\Model;

use Framework\Exceptions\CoreException;
use PDO;

class Storage
{
    private static $config = false;
    private static $instance = null;

    /**
     * @var PDO
     */
    private $db = false;

    private $dbConfig = [];

    /**
     * Storage constructor.
     * @param $dbConfig
     */
    public function __construct($dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    public static function initialize($config)
    {
        self::$config = $config;
    }

    /**
     * @return Storage|null
     * @throws CoreException
     */
    public static function getInstance()
    {
        if (self::$config === false) {
            throw new CoreException('Run Storage::initialize() before instance');
        }

        if (self::$instance !== null) {
            return self::$instance;
        }

        return new Storage(self::$config);
    }

    private function db()
    {
        if ($this->db === false) {
            $this->db = new PDO($this->dbConfig['dsn'], $this->dbConfig['user'], $this->dbConfig['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbConfig = [];
        }

        return $this->db;
    }

    public function fetch($key, $condition)
    {
        $sql = 'SELECT * FROM ' . $this->key($key) .
            $this->getSelectQueryEnd($condition, 0, 1);

        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute($this->params($condition))) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    public function fetchAll($key, array $condition = [], $from = 0, $length = false, $field = false, $sort = false)
    {
        $sql = 'SELECT * FROM ' . $this->key($key) .
            $this->getSelectQueryEnd($condition, $from, $length, $field, $sort);

        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute($this->params($condition))) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return false;
    }

    public function count($key, $condition)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->key($key) .
            $this->getSelectQueryEnd($condition);

        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute($this->params($condition))) {
            return $stmt->fetchColumn();
        }

        return false;
    }

    public function create($key, $params)
    {
        $sql = 'INSERT INTO ' . $this->key($key) .
            ' (' . $this->keys($params) . ') VALUES(' . $this->values($params) . ')';
        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute($this->params($params))) {
            return $this->db()->lastInsertId();
        }

        return false;
    }

    public function update($key, $condition, $params)
    {
        $sql = 'UPDATE ' . $this->key($key) . ' SET ' . $this->prepareCondition($params, ', ') .
            $this->getSelectQueryEnd($condition);

        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute(array_merge($this->params($params), $this->params($condition)))) {
            return $this->db()->lastInsertId();
        }

        return false;
    }

    public function destroy($key, $condition)
    {
        /* nothing */
    }

    private function key($name)
    {
        return '`' . $name . '`';
    }

    private function getSelectQueryEnd(array $condition = [],
                                       $from = 0,
                                       $length = false,
                                       $field = false,
                                       $sort = false)
    {
        $query = [' WHERE '];

        if ($condition) {
            $query[] = $this->prepareCondition($condition);
        } else $query[] = '1 = 1';

        if ($sort !== false) $query[] = 'ORDER BY ' . $this->key($field) . ' ' . $sort;

        if ($length !== false) $query[] = 'LIMIT ' . $length;
        if ($from > 0) $query[] = 'OFFSET ' . $from;

        return implode(PHP_EOL, $query);
    }

    protected function prepareCondition(array $condition, $delimiter = ' ')
    {
        array_walk($condition, function(&$rule, $key) {
            $rule = $this->key($key) . ' = ' . ':' . ltrim($key, ':');
        });

        return implode($delimiter, $condition);
    }

    protected function keys($params)
    {
        $keys = array_keys($params);

        $keys = array_map(function($value) {
            return $this->key(ltrim($value, ':'));
        }, $keys);

        return implode(', ', $keys);
    }

    protected function values($params)
    {
        if ($params) return implode(', ', $this->prepareKeys($params));
        return null;
    }

    protected function prepareKeys($params)
    {
        $keys = array_keys($params);

        $keys = array_map(function($value) {
            return ':' . ltrim($value, ':');
        }, $keys);

        return $keys;
    }

    protected function params(array $params)
    {
        $keys = $this->prepareKeys($params);
        return array_combine($keys, array_values($params));
    }
}