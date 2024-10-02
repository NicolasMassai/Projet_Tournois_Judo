<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241002083427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tournoi_club (tournoi_id INT NOT NULL, club_id INT NOT NULL, INDEX IDX_E50DD4F6F607770A (tournoi_id), INDEX IDX_E50DD4F661190A32 (club_id), PRIMARY KEY(tournoi_id, club_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournoi_club ADD CONSTRAINT FK_E50DD4F6F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi_club ADD CONSTRAINT FK_E50DD4F661190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi_club DROP FOREIGN KEY FK_E50DD4F6F607770A');
        $this->addSql('ALTER TABLE tournoi_club DROP FOREIGN KEY FK_E50DD4F661190A32');
        $this->addSql('DROP TABLE tournoi_club');
    }
}
