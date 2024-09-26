<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926192211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arbitre DROP FOREIGN KEY FK_20B2E66E5CF908A2');
        $this->addSql('DROP TABLE affectation_arbitre');
        $this->addSql('DROP INDEX IDX_20B2E66E5CF908A2 ON arbitre');
        $this->addSql('ALTER TABLE arbitre DROP affectation_arbitre_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectation_arbitre (id INT AUTO_INCREMENT NOT NULL, date_affectation DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE arbitre ADD affectation_arbitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arbitre ADD CONSTRAINT FK_20B2E66E5CF908A2 FOREIGN KEY (affectation_arbitre_id) REFERENCES affectation_arbitre (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_20B2E66E5CF908A2 ON arbitre (affectation_arbitre_id)');
    }
}
