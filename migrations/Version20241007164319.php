<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007164319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupe_adherant (groupe_id INT NOT NULL, adherant_id INT NOT NULL, INDEX IDX_67853CC7A45358C (groupe_id), INDEX IDX_67853CCBE612E45 (adherant_id), PRIMARY KEY(groupe_id, adherant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE groupe_adherant ADD CONSTRAINT FK_67853CC7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_adherant ADD CONSTRAINT FK_67853CCBE612E45 FOREIGN KEY (adherant_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe_adherant DROP FOREIGN KEY FK_67853CC7A45358C');
        $this->addSql('ALTER TABLE groupe_adherant DROP FOREIGN KEY FK_67853CCBE612E45');
        $this->addSql('DROP TABLE groupe_adherant');
    }
}
