<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620091513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform() === 'sqlite', 'SQLite is not supported for this migration');

        $this->addSql('ALTER TABLE users CHANGE games games JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE platforms platforms JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform() === 'sqlite', 'SQLite is not supported for this migration');

        $this->addSql('ALTER TABLE users CHANGE games games LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE platforms platforms LONGTEXT DEFAULT NULL');
    }
}
