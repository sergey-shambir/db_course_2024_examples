<?php
declare(strict_types=1);

namespace App\Database;

use App\Domain\Account;

class AccountRepository
{
    public function findByPhone(string $phone): ?Account
    {
        $connection = ConnectionProvider::getConnection();
        $result = $connection->execute(
            <<<SQL
            SELECT
              phone,
              balance,
              sms_count,
              minutes_count
            FROM account
            WHERE phone = :phone
            SQL,
            [':phone' => $phone]
        )->fetch(\PDO::FETCH_ASSOC);

        return $result ? $this->hydrateAccount($result) : null;
    }

    public function store(Account $account): void
    {
        $connection = ConnectionProvider::getConnection();
        $connection->execute(
            <<<SQL
            INSERT INTO account
              (phone, balance, sms_count, minutes_count)
            VALUES
              (:phone, :balance, :sms_count, :minutes_count)
            ON DUPLICATE KEY UPDATE
              balance = VALUES(balance),
              sms_count = VALUES(sms_count),
              minutes_count = VALUES(minutes_count)
            SQL,
            [
                ':phone' => $account->getPhone(),
                ':balance' => $account->getBalance(),
                ':sms_count' => $account->getSmsCount(),
                ':minutes_count' => $account->getMinutesCount(),
            ]
        );
    }

    private function hydrateAccount(array $result): Account
    {
        return new Account(
            phone: $result['phone'],
            balance: (float)$result['balance'],
            smsCount: (int)$result['sms_count'],
            minutesCount: (int)$result['minutes_count']
        );
    }
}
