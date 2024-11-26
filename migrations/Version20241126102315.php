<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126102315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398AF0DA6F0');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D64B0BCC96');
        $this->addSql('ALTER TABLE tournoi_adherant DROP FOREIGN KEY FK_3590DD72BE612E45');
        $this->addSql('ALTER TABLE tournoi_adherant DROP FOREIGN KEY FK_3590DD72F607770A');
        $this->addSql('ALTER TABLE historique_combat DROP FOREIGN KEY FK_441343C44B0BCC96');
        $this->addSql('ALTER TABLE tournoi_categorie DROP FOREIGN KEY FK_35037306BCF5E72D');
        $this->addSql('ALTER TABLE tournoi_categorie DROP FOREIGN KEY FK_35037306F607770A');
        $this->addSql('ALTER TABLE combattant DROP FOREIGN KEY FK_C3E38EF661190A32');
        $this->addSql('DROP TABLE tournoi_adherant');
        $this->addSql('DROP TABLE historique_combat');
        $this->addSql('DROP TABLE tournoi_categorie');
        $this->addSql('DROP TABLE combattant');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398BCF5E72D');
        $this->addSql('DROP INDEX IDX_8D51E398AF0DA6F0 ON combat');
        $this->addSql('DROP INDEX IDX_8D51E398BCF5E72D ON combat');
        $this->addSql('ALTER TABLE combat DROP categorie_id, DROP historique_combat_id');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21BCF5E72D');
        $this->addSql('DROP INDEX IDX_4B98C21BCF5E72D ON groupe');
        $this->addSql('ALTER TABLE groupe DROP categorie_id');
        $this->addSql('DROP INDEX UNIQ_5E90F6D64B0BCC96 ON inscription');
        $this->addSql('ALTER TABLE inscription DROP combattant_id');
        $this->addSql('ALTER TABLE user DROP qualification');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tournoi_adherant (tournoi_id INT NOT NULL, adherant_id INT NOT NULL, INDEX IDX_3590DD72BE612E45 (adherant_id), INDEX IDX_3590DD72F607770A (tournoi_id), PRIMARY KEY(tournoi_id, adherant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE historique_combat (id INT AUTO_INCREMENT NOT NULL, combattant_id INT DEFAULT NULL, resultat VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, points INT NOT NULL, date_combat DATE NOT NULL, victoire INT DEFAULT NULL, defaite INT DEFAULT NULL, INDEX IDX_441343C44B0BCC96 (combattant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tournoi_categorie (tournoi_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_35037306BCF5E72D (categorie_id), INDEX IDX_35037306F607770A (tournoi_id), PRIMARY KEY(tournoi_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE combattant (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, age INT NOT NULL, poids DOUBLE PRECISION NOT NULL, classement INT NOT NULL, INDEX IDX_C3E38EF661190A32 (club_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tournoi_adherant ADD CONSTRAINT FK_3590DD72BE612E45 FOREIGN KEY (adherant_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi_adherant ADD CONSTRAINT FK_3590DD72F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE historique_combat ADD CONSTRAINT FK_441343C44B0BCC96 FOREIGN KEY (combattant_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE tournoi_categorie ADD CONSTRAINT FK_35037306BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournoi_categorie ADD CONSTRAINT FK_35037306F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE combattant ADD CONSTRAINT FK_C3E38EF661190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE groupe ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4B98C21BCF5E72D ON groupe (categorie_id)');
        $this->addSql('ALTER TABLE user ADD qualification VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD categorie_id INT DEFAULT NULL, ADD historique_combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398AF0DA6F0 FOREIGN KEY (historique_combat_id) REFERENCES historique_combat (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D51E398AF0DA6F0 ON combat (historique_combat_id)');
        $this->addSql('CREATE INDEX IDX_8D51E398BCF5E72D ON combat (categorie_id)');
        $this->addSql('ALTER TABLE inscription ADD combattant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D64B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E90F6D64B0BCC96 ON inscription (combattant_id)');
    }
}
