<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Application\AccountService;
use App\Database\AccountBalanceTransferRepository;
use App\Database\AccountRepository;
use App\Database\ConnectionProvider;
use App\Domain\Account;
use App\Tests\Common\AbstractDatabaseTestCase;

class AccountServiceTest extends AbstractDatabaseTestCase
{
    private AccountRepository $accountRepository;
    private AccountBalanceTransferRepository $accountBalanceTransferRepository;
    private AccountService $accountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountRepository = new AccountRepository();
        $this->accountBalanceTransferRepository = new AccountBalanceTransferRepository();
        $this->accountService = new AccountService(
            ConnectionProvider::getConnection(),
            $this->accountRepository,
            $this->accountBalanceTransferRepository
        );
    }

    public function testTransferMoney(): void
    {
        $fromPhone = '+78362685453';
        $toPhone = '+78362685445';

        $this->accountService->createAccount($fromPhone, 600);
        $this->accountService->createAccount($toPhone, 100);
        $this->accountService->transferMoney($fromPhone, $toPhone, 400);

        $fromAccount = $this->accountRepository->findByPhone($fromPhone);
        $toAccount = $this->accountRepository->findByPhone($toPhone);
        $this->assertAccount($fromAccount, phone: $fromPhone, balance: 200);
        $this->assertAccount($toAccount, phone: $toPhone, balance: 500);
    }

    private function assertAccount(Account $account, string $phone, float $balance, int $smsCount = 0, int $minutesCount = 0): void
    {
        $this->assertEquals($phone, $account->getPhone());
        $this->assertEquals($balance, $account->getBalance());
        $this->assertEquals($smsCount, $account->getSmsCount());
        $this->assertEquals($minutesCount, $account->getMinutesCount());
    }
}
