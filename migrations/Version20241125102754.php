<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125102754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE arbitre_categorie (arbitre_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_D98D53CD943A5F0 (arbitre_id), INDEX IDX_D98D53CDBCF5E72D (categorie_id), PRIMARY KEY(arbitre_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE arbitre_categorie ADD CONSTRAINT FK_D98D53CD943A5F0 FOREIGN KEY (arbitre_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE arbitre_categorie ADD CONSTRAINT FK_D98D53CDBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arbitre_categorie DROP FOREIGN KEY FK_D98D53CD943A5F0');
        $this->addSql('ALTER TABLE arbitre_categorie DROP FOREIGN KEY FK_D98D53CDBCF5E72D');
        $this->addSql('DROP TABLE arbitre_categorie');
    }
}
