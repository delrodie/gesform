<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010090108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Formation (id INT AUTO_INCREMENT NOT NULL, candidat_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, titularisation VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, createdAt DATETIME DEFAULT NULL, INDEX IDX_C2B1A31C8D0EB82 (candidat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Formation ADD CONSTRAINT FK_C2B1A31C8D0EB82 FOREIGN KEY (candidat_id) REFERENCES Candidat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Formation');
    }
}
