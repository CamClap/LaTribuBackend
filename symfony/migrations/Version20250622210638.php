<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622210638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invitation ADD group_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F11D61A2FE54D947 ON invitation (group_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2FE54D947
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F11D61A2FE54D947 ON invitation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invitation DROP group_id
        SQL);
    }
}
