<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230318053941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `article` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE article
            (
              id INT UNSIGNED AUTO_INCREMENT,
              version INT UNSIGNED NOT NULL DEFAULT 1,
              title VARCHAR(200),
              content MEDIUMTEXT,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              created_by INT UNSIGNED NOT NULL,
              updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              updated_by INT UNSIGNED,
              PRIMARY KEY (id)
            )
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article');
    }
}
