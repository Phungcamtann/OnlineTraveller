<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * Singleton PDO MySQL/MariaDB database wrapper for OnlineTraveller.
 * Exposes connection management, prepared queries, transaction control,
 * and robust Stored Procedure execution.
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public static function init(array $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new RuntimeException('Database has not been initialized. Call Database::init($config) first.');
        }
        return self::$instance;
    }

    public static function getConnection(): PDO
    {
        return self::getInstance()->getPdo();
    }

    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }

    private function connect(): void
    {
        $host = $this->config['host'] ?? '127.0.0.1';
        $port = (string)($this->config['port'] ?? '3306');
        $dbname = $this->config['name'] ?? 'online_traveller';
        $charset = $this->config['charset'] ?? 'utf8mb4';
        $user = $this->config['user'] ?? 'root';
        $password = $this->config['password'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed [{$host}:{$port}/{$dbname}]: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        if (!$this->getPdo()->inTransaction()) {
            return $this->getPdo()->beginTransaction();
        }
        return false;
    }

    public function commit(): bool
    {
        if ($this->getPdo()->inTransaction()) {
            return $this->getPdo()->commit();
        }
        return false;
    }

    public function rollBack(): bool
    {
        if ($this->getPdo()->inTransaction()) {
            return $this->getPdo()->rollBack();
        }
        return false;
    }

    public function inTransaction(): bool
    {
        return $this->getPdo()->inTransaction();
    }

    /**
     * Executes a Stored Procedure with positional parameters and clears all result sets.
     * Returns an array of result rows from the primary result set.
     */
    public function callProcedure(string $procedureName, array $params = []): array
    {
        $placeholders = empty($params) ? '' : implode(', ', array_fill(0, count($params), '?'));
        $sql = "CALL {$procedureName}({$placeholders})";

        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute(array_values($params));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Advance and close any pending cursor/result set to avoid "Commands out of sync"
        while ($stmt->nextRowset()) {
            // drain extra result sets if any
        }
        $stmt->closeCursor();

        return $rows;
    }
}

if (!class_exists('Database', false)) {
    class_alias(Database::class, 'Database');
}
