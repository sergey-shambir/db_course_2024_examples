<?php
declare(strict_types=1);

namespace App\Database;

class DatabaseLock
{
    private const MAX_MYSQL_LOCK_NAME_LENGTH = 64;
    private const MD5_SUM_LENGTH = 32;

    public function __construct(
        private readonly Connection $connection,
        private string $lockName,
        private readonly int $timeoutSeconds)
    {
        $this->lockName = self::fixLongLockName($this->lockName);
    }

    public function lock(): void
    {
        $result = $this->connection->execute(
            'SELECT GET_LOCK(?, ?)',
            [$this->lockName, $this->timeoutSeconds]
        )->fetchColumn();

        if (!$result)
        {
            throw new \RuntimeException("Failed to get lock {$this->lockName} in {$this->timeoutSeconds} seconds");
        }
    }

    public function unlock(): void
    {
        $this->connection->execute(
            'SELECT RELEASE_LOCK(?)',
            [$this->lockName]
        );
    }

    private static function fixLongLockName(string $lockName): string
    {
        // MySQL не допускает именованные блокировки с длиной имени более 64 байт.
        // Если длина имени превышает 64 байта, то оставим первые 32 байта, а остальное заменим MD5-суммой.
        if (strlen($lockName) > self::MAX_MYSQL_LOCK_NAME_LENGTH)
        {
            $prefixLength = self::MAX_MYSQL_LOCK_NAME_LENGTH - self::MD5_SUM_LENGTH;
            $prefix = substr($lockName, 0, $prefixLength);
            $suffix = substr($lockName, $prefixLength);
            $lockName = $prefix . md5($suffix);
        }
        return $lockName;
    }
}
