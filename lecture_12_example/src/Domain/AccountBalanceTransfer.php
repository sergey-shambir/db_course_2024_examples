<?php
declare(strict_types=1);

namespace App\Domain;

readonly class AccountBalanceTransfer
{
    public function __construct(
        private string $fromPhone,
        private string $toPhone,
        private float $amount
    )
    {
    }

    public function getFromPhone(): string
    {
        return $this->fromPhone;
    }

    public function getToPhone(): string
    {
        return $this->toPhone;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
