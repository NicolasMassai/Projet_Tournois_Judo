<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125115352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe ADD categorie_tournoi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C2168CEBDA6 FOREIGN KEY (categorie_tournoi_id) REFERENCES categorie_tournoi (id)');
        $this->addSql('CREATE INDEX IDX_4B98C2168CEBDA6 ON groupe (categorie_tournoi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2168CEBDA6');
        $this->addSql('DROP INDEX IDX_4B98C2168CEBDA6 ON groupe');
        $this->addSql('ALTER TABLE groupe DROP categorie_tournoi_id');
    }
}
