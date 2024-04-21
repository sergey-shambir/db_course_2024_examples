<?php
declare(strict_types=1);

namespace App\Database;

final class Connection
{
    private \PDO $pdo;
    private int $transactionLevel = 0;

    /**
     * @param string $dsn - DSN, например 'mysql:dbname=testdb;host=127.0.0.1'
     * @param string $user - имя пользователя MySQL
     * @param string $password - пароль пользователя MySQL
     */
    public function __construct(string $dsn, string $user, string $password)
    {
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Выполняет запрос с подстановкой параметров.
     * Подстановка параметров даёт устойчивость к SQL Injections.
     *
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function execute(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function getLastInsertId(): int
    {
        if ($lastInsertId = $this->pdo->lastInsertId())
        {
            return (int)$lastInsertId;
        }
        return throw new \RuntimeException("Failed to get last insert id");
    }

    public function beginTransaction(): void
    {
        if ($this->transactionLevel === 0)
        {
            $this->pdo->beginTransaction();
        }
        ++$this->transactionLevel;
    }

    public function commit(): void
    {
        if ($this->transactionLevel <= 0)
        {
            throw new \RuntimeException('Cannot call ' . __METHOD__ . ': there is no open transaction');
        }

        --$this->transactionLevel;
        if ($this->transactionLevel === 0)
        {
            $this->pdo->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->transactionLevel <= 0)
        {
            throw new \RuntimeException('Cannot call ' . __METHOD__ . ': there is no open transaction');
        }

        --$this->transactionLevel;
        if ($this->transactionLevel === 0)
        {
            $this->pdo->rollBack();
        }
    }
}

