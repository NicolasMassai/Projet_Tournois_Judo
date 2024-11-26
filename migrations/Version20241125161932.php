<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125161932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat ADD categorie_tournoi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E39868CEBDA6 FOREIGN KEY (categorie_tournoi_id) REFERENCES categorie_tournoi (id)');
        $this->addSql('CREATE INDEX IDX_8D51E39868CEBDA6 ON combat (categorie_tournoi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E39868CEBDA6');
        $this->addSql('DROP INDEX IDX_8D51E39868CEBDA6 ON combat');
        $this->addSql('ALTER TABLE combat DROP categorie_tournoi_id');
    }
}
