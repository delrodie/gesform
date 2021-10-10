<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010090007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Candidat ADD region_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Candidat ADD CONSTRAINT FK_93C3D62798260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE INDEX IDX_93C3D62798260155 ON Candidat (region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Candidat DROP FOREIGN KEY FK_93C3D62798260155');
        $this->addSql('DROP INDEX IDX_93C3D62798260155 ON Candidat');
        $this->addSql('ALTER TABLE Candidat DROP region_id');
    }
}
