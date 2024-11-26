<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125114505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_tournoi_adherant (categorie_tournoi_id INT NOT NULL, adherant_id INT NOT NULL, INDEX IDX_423C8EB68CEBDA6 (categorie_tournoi_id), INDEX IDX_423C8EBBE612E45 (adherant_id), PRIMARY KEY(categorie_tournoi_id, adherant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie_tournoi_adherant ADD CONSTRAINT FK_423C8EB68CEBDA6 FOREIGN KEY (categorie_tournoi_id) REFERENCES categorie_tournoi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_tournoi_adherant ADD CONSTRAINT FK_423C8EBBE612E45 FOREIGN KEY (adherant_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_tournoi_adherant DROP FOREIGN KEY FK_423C8EB68CEBDA6');
        $this->addSql('ALTER TABLE categorie_tournoi_adherant DROP FOREIGN KEY FK_423C8EBBE612E45');
        $this->addSql('DROP TABLE categorie_tournoi_adherant');
    }
}
