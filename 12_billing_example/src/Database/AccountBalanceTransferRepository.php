<?php
declare(strict_types=1);

namespace App\Database;

use App\Domain\AccountBalanceTransfer;

class AccountBalanceTransferRepository
{
    public function add(AccountBalanceTransfer $transfer): void
    {
        $connection = ConnectionProvider::getConnection();
        $connection->execute(
            <<<SQL
            INSERT INTO account_balance_transfer
              (from_phone, to_phone, amount)
            VALUES (:from_phone, :to_phone, :amount)
            SQL,

            [
                ':from_phone' => $transfer->getFromPhone(),
                ':to_phone' => $transfer->getToPhone(),
                ':amount' => $transfer->getAmount()
            ]
        );
    }
}
