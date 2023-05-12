<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503111422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livre_livre DROP FOREIGN KEY FK_3F60D7FFE22DE809');
        $this->addSql('ALTER TABLE livre_livre DROP FOREIGN KEY FK_3F60D7FFFBC8B886');
        $this->addSql('DROP TABLE livre_livre');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livre_livre (livre_source INT NOT NULL, livre_target INT NOT NULL, INDEX IDX_3F60D7FFFBC8B886 (livre_source), INDEX IDX_3F60D7FFE22DE809 (livre_target), PRIMARY KEY(livre_source, livre_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE livre_livre ADD CONSTRAINT FK_3F60D7FFE22DE809 FOREIGN KEY (livre_target) REFERENCES livre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livre_livre ADD CONSTRAINT FK_3F60D7FFFBC8B886 FOREIGN KEY (livre_source) REFERENCES livre (id) ON DELETE CASCADE');
    }
}
