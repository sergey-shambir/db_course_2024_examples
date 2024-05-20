<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230318053942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `tag` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE tag
            (
              id INT UNSIGNED AUTO_INCREMENT,
              text VARCHAR(200) NOT NULL,
              UNIQUE KEY (text),
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id)
            )
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tag');
    }
}
