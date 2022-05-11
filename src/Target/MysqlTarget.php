<?php

namespace Xinger\Target;

use Connector\Connector;
use PDO;

class MysqlTarget
{
    private $pdo;
    private $dsn;
    private $tableName;

    public function __construct(string $dsn, string $tableName)
    {
        $this->dsn = $dsn;
        $this->tableName = $tableName;
    }

    public function open()
    {
        $connector = new Connector();
        $config = $connector->getConfig($this->dsn);
        if (!$connector->exists($config)) {
            throw new RuntimeException("Connection failed. schema uninitialized?");
        }
        $this->pdo = $connector->getPdo($config);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applyRecord(array $record): bool
    {
        $values = $record['record'];
        $this->insert($values);
        // print_r($record);
        // echo $record['stream'] . PHP_EOL;
        return true;
    }

    public function close()
    {
        $this->pdo = null;
    }


    public function insert(array $values)
    {
        $setters = $this->arrayToSetters($values);

        $sql = sprintf(
            "INSERT INTO %s SET %s",
            $this->tableName,
            $setters
        );
        $statement = $this->pdo->prepare($sql);
        $statement->execute($values);
    }


    protected function arrayToSetters(array $a) {
        $sql = '';
        foreach ($a as $key => $value) {
            if ($sql != '') {
                $sql .= ', ';
            }
            $sql .= sprintf('%s=:%s', $key, $key);
        }
        return $sql;
    }
}
