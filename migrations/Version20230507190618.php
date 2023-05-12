<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507190618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_livre (user_id INT NOT NULL, livre_id INT NOT NULL, INDEX IDX_4EA1B4C1A76ED395 (user_id), INDEX IDX_4EA1B4C137D925CB (livre_id), PRIMARY KEY(user_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_livre ADD CONSTRAINT FK_4EA1B4C137D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C1A76ED395');
        $this->addSql('ALTER TABLE user_livre DROP FOREIGN KEY FK_4EA1B4C137D925CB');
        $this->addSql('DROP TABLE user_livre');
    }
}
