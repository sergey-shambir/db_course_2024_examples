<?php
declare(strict_types=1);

namespace App\Domain;

class Account
{
    public function __construct(
        private string $phone,
        private float $balance = 0.0,
        private int $smsCount = 0,
        private int $minutesCount = 0
    )
    {
    }

    public function transferMoney(Account $targetAccount, float $amount): void
    {
        if ($this->balance < $amount)
        {
            throw new \LogicException("Cannot transfer money: account {$this->phone} balance is less than transfer amount $amount");
        }

        $this->balance -= $amount;
        $targetAccount->balance += $amount;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getSmsCount(): int
    {
        return $this->smsCount;
    }

    public function getMinutesCount(): int
    {
        return $this->minutesCount;
    }
}

