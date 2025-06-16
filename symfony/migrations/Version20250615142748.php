<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615142748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD creator_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6DC044C561220EA6 ON `group` (creator_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561220EA6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6DC044C561220EA6 ON `group`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `group` DROP creator_id
        SQL);
    }
}
