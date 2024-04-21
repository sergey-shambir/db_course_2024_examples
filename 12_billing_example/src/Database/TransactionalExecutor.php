<?php
declare(strict_types=1);

namespace App\Database;

class TransactionalExecutor
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Метод выполняет переданную функцию в транзакции и под именованной блокировкой, в конце освобождая блокировку.
     *
     * @param string $lockName - название именованной блокировки
     * @param int $timeoutSeconds - максимальное время ожидания блокировки
     * @param \Closure $action - функция, которую нужно выполнить
     * @return mixed|void
     */
    public function doWithTransactionAndLock(string $lockName, int $timeoutSeconds, \Closure $action)
    {
        return $this->doWithTransaction(fn() => $this->doWithLock($lockName, $timeoutSeconds, $action));
    }

    /**
     * Метод выполняет переданную функцию под именованной блокировкой, в конце освобождая блокировку.
     *
     * @param string $lockName - название именованной блокировки
     * @param int $timeoutSeconds - максимальное время ожидания блокировки
     * @param \Closure $action - функция, которую нужно выполнить
     * @return void
     */
    public function doWithLock(string $lockName, int $timeoutSeconds, \Closure $action)
    {
        $lock = new DatabaseLock($this->connection, $lockName, $timeoutSeconds);
        $lock->lock();
        try
        {
            return $action();
        }
        finally
        {
            $lock->unlock();
        }
    }

    /**
     * Метод выполняет переданную функцию внутри открытой транзакции, в конце вызывая COMMIT либо ROLLBACK.
     *
     * @param \Closure $action - функция, которую нужно выполнить
     * @return mixed|void
     */
    public function doWithTransaction(\Closure $action)
    {
        $this->connection->beginTransaction();
        $commit = false;
        try
        {
            $result = $action();
            $commit = true;
            return $result;
        }
        finally
        {
            if ($commit)
            {
                $this->connection->commit();
            }
            else
            {
                $this->connection->rollBack();
            }
        }
    }
}
