<?php
declare(strict_types=1);

namespace App\Tests\Common;

use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{
    private \PDO $connection;

    // Вызывается перед каждым тестирующим методом
    protected function setUp(): void
    {
        parent::setUp();
        // Всегда начинаем транзакцию, чтобы не применять изменений к базе данных.
        $this->connection = \connectDatabase();
        $this->connection->beginTransaction();
    }

    // Вызывается после каждого тестирующего метода
    protected function tearDown(): void
    {
        // Всегда откатываем транзакцию, чтобы не применять изменений к базе данных.
        $this->connection->rollback();
        parent::tearDown();
    }
}
