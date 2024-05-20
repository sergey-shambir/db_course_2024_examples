<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230318053943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `article_tag` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE article_tag
            (
              article_id INT UNSIGNED,
              tag_id INT UNSIGNED,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (article_id, tag_id),
              FOREIGN KEY fk_article_id_key (article_id)
                REFERENCES article (id)
                ON UPDATE CASCADE ON DELETE CASCADE,
              FOREIGN KEY fk_tag_id_key (tag_id)
                REFERENCES tag (id)
                ON UPDATE CASCADE ON DELETE CASCADE
            )
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article_tag');
    }
}
