<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241002151311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tournoi_adherant (tournoi_id INT NOT NULL, adherant_id INT NOT NULL, INDEX IDX_3590DD72F607770A (tournoi_id), INDEX IDX_3590DD72BE612E45 (adherant_id), PRIMARY KEY(tournoi_id, adherant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournoi_adherant ADD CONSTRAINT FK_3590DD72F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi_adherant ADD CONSTRAINT FK_3590DD72BE612E45 FOREIGN KEY (adherant_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi_adherant DROP FOREIGN KEY FK_3590DD72F607770A');
        $this->addSql('ALTER TABLE tournoi_adherant DROP FOREIGN KEY FK_3590DD72BE612E45');
        $this->addSql('DROP TABLE tournoi_adherant');
    }
}
