<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010042608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Anomalie (id INT AUTO_INCREMENT NOT NULL, adherant_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, montant INT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, paiment VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, paiementDate VARCHAR(255) DEFAULT NULL, responseId VARCHAR(255) DEFAULT NULL, createdAt DATETIME DEFAULT NULL, INDEX IDX_882CC3CABE612E45 (adherant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Anomalie ADD CONSTRAINT FK_882CC3CABE612E45 FOREIGN KEY (adherant_id) REFERENCES Adherant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Anomalie');
    }
}
