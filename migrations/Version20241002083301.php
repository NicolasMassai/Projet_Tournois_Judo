<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241002083301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF61190A32');
        $this->addSql('DROP INDEX IDX_18AFD9DF61190A32 ON tournoi');
        $this->addSql('ALTER TABLE tournoi DROP club_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_18AFD9DF61190A32 ON tournoi (club_id)');
    }
}
