<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127095008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD spectateur_id INT DEFAULT NULL, ADD combat_id INT DEFAULT NULL, DROP commentaire, DROP date');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14AF471192 FOREIGN KEY (spectateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14FC7EEDB8 FOREIGN KEY (combat_id) REFERENCES combat (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14AF471192 ON note (spectateur_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14FC7EEDB8 ON note (combat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14AF471192');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14FC7EEDB8');
        $this->addSql('DROP INDEX IDX_CFBDFA14AF471192 ON note');
        $this->addSql('DROP INDEX IDX_CFBDFA14FC7EEDB8 ON note');
        $this->addSql('ALTER TABLE note ADD commentaire LONGTEXT DEFAULT NULL, ADD date DATE NOT NULL, DROP spectateur_id, DROP combat_id');
    }
}
