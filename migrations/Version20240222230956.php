<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222230956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificate ADD cours_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE certificate ADD CONSTRAINT FK_219CDA4A7ECF78B0 FOREIGN KEY (cours_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_219CDA4A7ECF78B0 ON certificate (cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certificate DROP FOREIGN KEY FK_219CDA4A7ECF78B0');
        $this->addSql('DROP INDEX IDX_219CDA4A7ECF78B0 ON certificate');
        $this->addSql('ALTER TABLE certificate DROP cours_id');
    }
}
