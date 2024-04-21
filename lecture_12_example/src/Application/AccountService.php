<?php
declare(strict_types=1);

namespace App\Application;

use App\Database\AccountBalanceTransferRepository;
use App\Database\AccountRepository;
use App\Database\Connection;
use App\Database\DatabaseLock;
use App\Database\TransactionalExecutor;
use App\Domain\Account;
use App\Domain\AccountBalanceTransfer;

readonly class AccountService
{
    public function __construct(
        private Connection $connection,
        private AccountRepository $accountRepository,
        private AccountBalanceTransferRepository $accountBalanceTransferRepository
    )
    {
    }

    public function createAccount(string $phone, float $balance = 0.0): void
    {
        $account = new Account($phone, $balance);
        $this->accountRepository->store($account);
    }

    public function transferMoney(string $fromPhone, string $toPhone, float $amount): void
    {
        $this->transferMoneyV1($fromPhone, $toPhone, $amount);
    }

    // Первая версия: без транзакций и блокировок
    public function transferMoneyV1(string $fromPhone, string $toPhone, float $amount): void
    {
        $fromAccount = $this->accountRepository->findByPhone($fromPhone);
        if (!$fromAccount)
        {
            throw new \InvalidArgumentException("No account with phone $fromPhone");
        }

        $toAccount = $this->accountRepository->findByPhone($toPhone);
        if (!$toAccount)
        {
            throw new \InvalidArgumentException("No account with phone $toPhone");
        }


        $fromAccount->transferMoney($toAccount, $amount);
        $this->accountRepository->store($fromAccount);
        $this->accountRepository->store($toAccount);

        $transfer = new AccountBalanceTransfer($fromPhone, $toPhone, $amount);
        $this->accountBalanceTransferRepository->add($transfer);
    }

    // Вторая версия: с транзакцией
    public function transferMoneyV2(string $fromPhone, string $toPhone, float $amount): void
    {
        $this->connection->beginTransaction();
        try
        {
            $fromAccount = $this->accountRepository->findByPhone($fromPhone);
            if (!$fromAccount)
            {
                throw new \InvalidArgumentException("No account with phone $fromPhone");
            }

            $toAccount = $this->accountRepository->findByPhone($toPhone);
            if (!$toAccount)
            {
                throw new \InvalidArgumentException("No account with phone $toPhone");
            }

            $fromAccount->transferMoney($toAccount, $amount);
            $this->accountRepository->store($fromAccount);
            $this->accountRepository->store($toAccount);

            $transfer = new AccountBalanceTransfer($fromPhone, $toPhone, $amount);
            $this->accountBalanceTransferRepository->add($transfer);
            $this->connection->commit();
        }
        catch (\Throwable $exception)
        {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    // Третья версия: с транзакцией и именованной блокировкой
    public function transferMoneyV3(string $fromPhone, string $toPhone, float $amount): void
    {
        $this->connection->beginTransaction();
        try
        {
            $lock = new DatabaseLock($this->connection, lockName: 'account_' . $fromPhone, timeoutSeconds: 10);
            $lock->lock();
            try
            {
                $fromAccount = $this->accountRepository->findByPhone($fromPhone);
                if (!$fromAccount)
                {
                    throw new \InvalidArgumentException("No account with phone $fromPhone");
                }

                $toAccount = $this->accountRepository->findByPhone($toPhone);
                if (!$toAccount)
                {
                    throw new \InvalidArgumentException("No account with phone $toPhone");
                }


                $fromAccount->transferMoney($toAccount, $amount);
                $this->accountRepository->store($fromAccount);
                $this->accountRepository->store($toAccount);

                $transfer = new AccountBalanceTransfer($fromPhone, $toPhone, $amount);
                $this->accountBalanceTransferRepository->add($transfer);
            }
            finally
            {
                $lock->unlock();
            }
            $this->connection->commit();
        }
        catch (\Throwable $exception)
        {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    // Четвёртая версия: с транзакцией и именованной блокировкой, с использованием TransactionalExecutor
    public function transferMoneyV4(string $fromPhone, string $toPhone, float $amount): void
    {
        $transactionalExecutor = new TransactionalExecutor($this->connection);
        $transactionalExecutor->doWithTransactionAndLock(
            lockName: 'account_' . $fromPhone,
            timeoutSeconds: 10,
            action: function () use ($fromPhone, $toPhone, $amount) {
                $fromAccount = $this->accountRepository->findByPhone($fromPhone);
                if (!$fromAccount)
                {
                    throw new \InvalidArgumentException("No account with phone $fromPhone");
                }

                $toAccount = $this->accountRepository->findByPhone($toPhone);
                if (!$toAccount)
                {
                    throw new \InvalidArgumentException("No account with phone $toPhone");
                }


                $fromAccount->transferMoney($toAccount, $amount);
                $this->accountRepository->store($fromAccount);
                $this->accountRepository->store($toAccount);

                $transfer = new AccountBalanceTransfer($fromPhone, $toPhone, $amount);
                $this->accountBalanceTransferRepository->add($transfer);
            }
        );
    }

}
