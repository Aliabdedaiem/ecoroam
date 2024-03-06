<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304113015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activities ADD events_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE59D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)');
        $this->addSql('CREATE INDEX IDX_B5F1AFE59D6A1065 ON activities (events_id)');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A2A4DB562');
        $this->addSql('DROP INDEX IDX_5387574A2A4DB562 ON events');
        $this->addSql('ALTER TABLE events DROP activities_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE59D6A1065');
        $this->addSql('DROP INDEX IDX_B5F1AFE59D6A1065 ON activities');
        $this->addSql('ALTER TABLE activities DROP events_id');
        $this->addSql('ALTER TABLE events ADD activities_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A2A4DB562 FOREIGN KEY (activities_id) REFERENCES activities (id)');
        $this->addSql('CREATE INDEX IDX_5387574A2A4DB562 ON events (activities_id)');
    }
}
