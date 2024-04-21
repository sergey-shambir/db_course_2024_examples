<?php
declare(strict_types=1);

namespace App\Database;

readonly class TransactionalExecutor
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * Метод выполняет переданную функцию внутри открытой транзакции, в конце вызывая COMMIT либо ROLLBACK.
     *
     * @param callable $action - функция, которую нужно выполнить
     * @return mixed|void
     */
    public function doWithTransaction(callable $action)
    {
        $this->connection->beginTransaction();
        try
        {
            $result = $action();
            $this->connection->commit();
            return $result;
        }
        catch (\Throwable $exception)
        {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * Метод выполняет переданную функцию под именованной блокировкой, в конце освобождая блокировку.
     *
     * @param string $lockName - название именованной блокировки
     * @param int $timeoutSeconds - максимальное время ожидания блокировки
     * @param callable $action - функция, которую нужно выполнить
     * @return void
     */
    public function doWithLock(string $lockName, int $timeoutSeconds, callable $action)
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
     * Метод выполняет переданную функцию в транзакции и под именованной блокировкой, в конце освобождая блокировку.
     *
     * @param string $lockName - название именованной блокировки
     * @param int $timeoutSeconds - максимальное время ожидания блокировки
     * @param callable $action - функция, которую нужно выполнить
     * @return mixed|void
     */
    public function doWithTransactionAndLock(string $lockName, int $timeoutSeconds, callable $action)
    {
        return $this->doWithTransaction(fn() => $this->doWithLock($lockName, $timeoutSeconds, $action));
    }
}
