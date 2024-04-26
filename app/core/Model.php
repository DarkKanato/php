<?php

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Runs a prepared query and returns the statement.
     */
    protected static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
