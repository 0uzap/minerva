<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213145211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ajouter (id INT AUTO_INCREMENT NOT NULL, panier_id INT DEFAULT NULL, livre_id INT DEFAULT NULL, INDEX IDX_AB384B5FF77D927C (panier_id), INDEX IDX_AB384B5F37D925CB (livre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, sujet VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, auteur VARCHAR(75) NOT NULL, prix DOUBLE PRECISION NOT NULL, date_publi DATETIME NOT NULL, format VARCHAR(50) NOT NULL, editeur VARCHAR(50) NOT NULL, langue VARCHAR(50) NOT NULL, couverture VARCHAR(100) NOT NULL, resume LONGTEXT NOT NULL, genre VARCHAR(100) NOT NULL, type VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, panier_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F77D927C (panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_livre (user_id INT NOT NULL, livre_id INT NOT NULL, INDEX IDX_4EA1B4C1A76ED395 (user_id), INDEX IDX_4EA1B4C137D925CB (livre_id), PRIMARY KEY(user_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT FK_AB384B5FF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT FK_AB384B5F37D925CB FOREIGN KEY (livre_id) REFERENCES livre (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C137D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY FK_AB384B5FF77D927C');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY FK_AB384B5F37D925CB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F77D927C');
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C1A76ED395');
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C137D925CB');
        $this->addSql('DROP TABLE ajouter');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE livre');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_livre');
    }
}
