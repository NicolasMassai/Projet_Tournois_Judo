<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004144326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat_combat DROP FOREIGN KEY FK_22D5DDF1E9EB1195');
        $this->addSql('ALTER TABLE combat_combat DROP FOREIGN KEY FK_22D5DDF1F00E411A');
        $this->addSql('DROP TABLE combat_combat');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398943A5F0');
        $this->addSql('DROP INDEX UNIQ_8D51E398943A5F0 ON combat');
        $this->addSql('ALTER TABLE combat ADD combattant2_id INT DEFAULT NULL, ADD score_combattant1 INT DEFAULT NULL, ADD score_combattant2 INT DEFAULT NULL, DROP date, CHANGE resultat resultat VARCHAR(255) DEFAULT NULL, CHANGE arbitre_id combattant1_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398CF4B102E FOREIGN KEY (combattant1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398DDFEBFC0 FOREIGN KEY (combattant2_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D51E398CF4B102E ON combat (combattant1_id)');
        $this->addSql('CREATE INDEX IDX_8D51E398DDFEBFC0 ON combat (combattant2_id)');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DFFC7EEDB8');
        $this->addSql('DROP INDEX IDX_18AFD9DFFC7EEDB8 ON tournoi');
        $this->addSql('ALTER TABLE tournoi DROP combat_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494B0BCC96');
        $this->addSql('DROP INDEX IDX_8D93D6494B0BCC96 ON user');
        $this->addSql('ALTER TABLE user DROP combattant_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE combat_combat (combat_source INT NOT NULL, combat_target INT NOT NULL, INDEX IDX_22D5DDF1E9EB1195 (combat_target), INDEX IDX_22D5DDF1F00E411A (combat_source), PRIMARY KEY(combat_source, combat_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE combat_combat ADD CONSTRAINT FK_22D5DDF1E9EB1195 FOREIGN KEY (combat_target) REFERENCES combat (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE combat_combat ADD CONSTRAINT FK_22D5DDF1F00E411A FOREIGN KEY (combat_source) REFERENCES combat (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD combattant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6494B0BCC96 ON user (combattant_id)');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398CF4B102E');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398DDFEBFC0');
        $this->addSql('DROP INDEX IDX_8D51E398CF4B102E ON combat');
        $this->addSql('DROP INDEX IDX_8D51E398DDFEBFC0 ON combat');
        $this->addSql('ALTER TABLE combat ADD arbitre_id INT DEFAULT NULL, ADD date DATE NOT NULL, DROP combattant1_id, DROP combattant2_id, DROP score_combattant1, DROP score_combattant2, CHANGE resultat resultat VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398943A5F0 FOREIGN KEY (arbitre_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D51E398943A5F0 ON combat (arbitre_id)');
        $this->addSql('ALTER TABLE tournoi ADD combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DFFC7EEDB8 FOREIGN KEY (combat_id) REFERENCES combat (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_18AFD9DFFC7EEDB8 ON tournoi (combat_id)');
    }
}
