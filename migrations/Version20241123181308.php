<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123181308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherant_categorie DROP FOREIGN KEY FK_E413DD96BCF5E72D');
        $this->addSql('ALTER TABLE adherant_categorie DROP FOREIGN KEY FK_E413DD96BE612E45');
        $this->addSql('DROP TABLE adherant_categorie');
        $this->addSql('ALTER TABLE user ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649BCF5E72D ON user (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adherant_categorie (adherant_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_E413DD96BCF5E72D (categorie_id), INDEX IDX_E413DD96BE612E45 (adherant_id), PRIMARY KEY(adherant_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE adherant_categorie ADD CONSTRAINT FK_E413DD96BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adherant_categorie ADD CONSTRAINT FK_E413DD96BE612E45 FOREIGN KEY (adherant_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BCF5E72D');
        $this->addSql('DROP INDEX IDX_8D93D649BCF5E72D ON user');
        $this->addSql('ALTER TABLE user DROP categorie_id');
    }
}
