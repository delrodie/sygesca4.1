<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010032849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Adherant (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, carte VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenoms VARCHAR(255) DEFAULT NULL, dateNaissance VARCHAR(255) DEFAULT NULL, lieuNaissance VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, urgence VARCHAR(255) DEFAULT NULL, contactUrgence VARCHAR(255) DEFAULT NULL, branche VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, cotisation VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, idTransaction VARCHAR(255) DEFAULT NULL, statusPaiement VARCHAR(255) DEFAULT NULL, createdAt DATETIME DEFAULT NULL, UpdatedAt DATETIME DEFAULT NULL, oldId INT DEFAULT NULL, result VARCHAR(255) DEFAULT NULL, INDEX IDX_6EAC3AEA7A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Compteur (id INT AUTO_INCREMENT NOT NULL, nombre INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Cotisation (id INT AUTO_INCREMENT NOT NULL, membre_id INT DEFAULT NULL, annee VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, carte VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, montantSansFrais INT DEFAULT NULL, ristourne INT DEFAULT NULL, createdAt DATETIME DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, INDEX IDX_E139D13D6A99F74A (membre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Membre (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, statut_id INT DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, carte VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenoms VARCHAR(255) DEFAULT NULL, dateNaissance VARCHAR(255) DEFAULT NULL, lieuNaissance VARCHAR(255) DEFAULT NULL, sexe VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, urgence VARCHAR(255) DEFAULT NULL, contactUrgence VARCHAR(255) DEFAULT NULL, branche VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, cotisation VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, INDEX IDX_F118FE1F7A45358C (groupe_id), INDEX IDX_F118FE1FF6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, doyenne VARCHAR(125) DEFAULT NULL, slug VARCHAR(255) NOT NULL, publie_par VARCHAR(25) DEFAULT NULL, modifie_par VARCHAR(25) DEFAULT NULL, publie_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, INDEX IDX_31C1548798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fonctions (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, montant VARCHAR(255) DEFAULT NULL, created_at VARCHAR(255) DEFAULT NULL, updated_at VARCHAR(255) DEFAULT NULL, ristourne INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, district_id INT DEFAULT NULL, paroisse VARCHAR(255) NOT NULL, localite VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, publie_par VARCHAR(25) DEFAULT NULL, modifie_par VARCHAR(25) DEFAULT NULL, publie_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, INDEX IDX_4B98C21B08FA272 (district_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE objectif (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, annee VARCHAR(255) NOT NULL, valeur INT NOT NULL, publie_par VARCHAR(25) DEFAULT NULL, modifie_par VARCHAR(25) DEFAULT NULL, publie_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, INDEX IDX_E2F8685198260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, slug VARCHAR(20) NOT NULL, publie_par VARCHAR(25) DEFAULT NULL, modifie_par VARCHAR(25) DEFAULT NULL, publie_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scout (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, statut_id INT DEFAULT NULL, matricule VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenoms VARCHAR(255) NOT NULL, datenaiss VARCHAR(255) NOT NULL, lieunaiss VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, branche VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, contactParent VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, carte VARCHAR(255) DEFAULT NULL, cotisation VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, publie_par VARCHAR(25) DEFAULT NULL, modifie_par VARCHAR(25) DEFAULT NULL, publie_le DATETIME DEFAULT NULL, modifie_le DATETIME DEFAULT NULL, urgence VARCHAR(255) DEFAULT NULL, INDEX IDX_176881647A45358C (groupe_id), INDEX IDX_17688164F6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Adherant ADD CONSTRAINT FK_6EAC3AEA7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE Cotisation ADD CONSTRAINT FK_E139D13D6A99F74A FOREIGN KEY (membre_id) REFERENCES Membre (id)');
        $this->addSql('ALTER TABLE Membre ADD CONSTRAINT FK_F118FE1F7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE Membre ADD CONSTRAINT FK_F118FE1FF6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C1548798260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE objectif ADD CONSTRAINT FK_E2F8685198260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE scout ADD CONSTRAINT FK_176881647A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE scout ADD CONSTRAINT FK_17688164F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Cotisation DROP FOREIGN KEY FK_E139D13D6A99F74A');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21B08FA272');
        $this->addSql('ALTER TABLE Adherant DROP FOREIGN KEY FK_6EAC3AEA7A45358C');
        $this->addSql('ALTER TABLE Membre DROP FOREIGN KEY FK_F118FE1F7A45358C');
        $this->addSql('ALTER TABLE scout DROP FOREIGN KEY FK_176881647A45358C');
        $this->addSql('ALTER TABLE district DROP FOREIGN KEY FK_31C1548798260155');
        $this->addSql('ALTER TABLE objectif DROP FOREIGN KEY FK_E2F8685198260155');
        $this->addSql('ALTER TABLE Membre DROP FOREIGN KEY FK_F118FE1FF6203804');
        $this->addSql('ALTER TABLE scout DROP FOREIGN KEY FK_17688164F6203804');
        $this->addSql('DROP TABLE Adherant');
        $this->addSql('DROP TABLE Compteur');
        $this->addSql('DROP TABLE Cotisation');
        $this->addSql('DROP TABLE Membre');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP TABLE fonctions');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE objectif');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE scout');
        $this->addSql('DROP TABLE statut');
    }
}
