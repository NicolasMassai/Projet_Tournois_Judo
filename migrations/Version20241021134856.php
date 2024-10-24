<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021134856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat ADD historique_combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398AF0DA6F0 FOREIGN KEY (historique_combat_id) REFERENCES historique_combat (id)');
        $this->addSql('CREATE INDEX IDX_8D51E398AF0DA6F0 ON combat (historique_combat_id)');
        $this->addSql('ALTER TABLE combattant DROP FOREIGN KEY FK_C3E38EF6AF0DA6F0');
        $this->addSql('DROP INDEX IDX_C3E38EF6AF0DA6F0 ON combattant');
        $this->addSql('ALTER TABLE combattant DROP historique_combat_id');
        $this->addSql('ALTER TABLE historique_combat DROP FOREIGN KEY FK_441343C4F607770A');
        $this->addSql('DROP INDEX UNIQ_441343C4F607770A ON historique_combat');
        $this->addSql('ALTER TABLE historique_combat ADD victoire INT DEFAULT NULL, ADD defaite INT DEFAULT NULL, CHANGE tournoi_id combattant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique_combat ADD CONSTRAINT FK_441343C44B0BCC96 FOREIGN KEY (combattant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_441343C44B0BCC96 ON historique_combat (combattant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combattant ADD historique_combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combattant ADD CONSTRAINT FK_C3E38EF6AF0DA6F0 FOREIGN KEY (historique_combat_id) REFERENCES historique_combat (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C3E38EF6AF0DA6F0 ON combattant (historique_combat_id)');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398AF0DA6F0');
        $this->addSql('DROP INDEX IDX_8D51E398AF0DA6F0 ON combat');
        $this->addSql('ALTER TABLE combat DROP historique_combat_id');
        $this->addSql('ALTER TABLE historique_combat DROP FOREIGN KEY FK_441343C44B0BCC96');
        $this->addSql('DROP INDEX IDX_441343C44B0BCC96 ON historique_combat');
        $this->addSql('ALTER TABLE historique_combat ADD tournoi_id INT DEFAULT NULL, DROP combattant_id, DROP victoire, DROP defaite');
        $this->addSql('ALTER TABLE historique_combat ADD CONSTRAINT FK_441343C4F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_441343C4F607770A ON historique_combat (tournoi_id)');
    }
}
