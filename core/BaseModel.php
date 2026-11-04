<?php

declare(strict_types=1);

namespace Core;

use PDO;

/**
 * Base Model providing database access methods and Stored Procedure caller shortcuts.
 */
abstract class BaseModel
{
    protected Database $db;
    protected PDO $pdo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getPdo();
    }

    /**
     * Call a MySQL Stored Procedure and return result rows.
     */
    protected function callProcedure(string $procedureName, array $params = []): array
    {
        return $this->db->callProcedure($procedureName, $params);
    }

    /**
     * Call a Stored Procedure expecting a single result row.
     */
    protected function callProcedureOne(string $procedureName, array $params = []): ?array
    {
        $rows = $this->callProcedure($procedureName, $params);
        return empty($rows) ? null : $rows[0];
    }

    /**
     * Execute a SELECT query and fetch single associative row.
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Execute a SELECT query and fetch all associative rows.
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Execute an INSERT / UPDATE / DELETE statement.
     */
    protected function execute(string $sql, array $params = []): int
    {
        return $this->db->execute($sql, $params);
    }

    /**
     * Return the last auto-incremented primary key.
     */
    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}

if (!class_exists('BaseModel', false)) {
    class_alias(BaseModel::class, 'BaseModel');
}
