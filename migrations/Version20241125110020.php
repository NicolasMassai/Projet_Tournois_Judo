<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125110020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_tournoi (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, tournoi_id INT DEFAULT NULL, INDEX IDX_472C777DBCF5E72D (categorie_id), INDEX IDX_472C777DF607770A (tournoi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_tournoi_arbitre (categorie_tournoi_id INT NOT NULL, arbitre_id INT NOT NULL, INDEX IDX_D0DD2CE368CEBDA6 (categorie_tournoi_id), INDEX IDX_D0DD2CE3943A5F0 (arbitre_id), PRIMARY KEY(categorie_tournoi_id, arbitre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie_tournoi ADD CONSTRAINT FK_472C777DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE categorie_tournoi ADD CONSTRAINT FK_472C777DF607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE categorie_tournoi_arbitre ADD CONSTRAINT FK_D0DD2CE368CEBDA6 FOREIGN KEY (categorie_tournoi_id) REFERENCES categorie_tournoi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_tournoi_arbitre ADD CONSTRAINT FK_D0DD2CE3943A5F0 FOREIGN KEY (arbitre_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE arbitre_categorie DROP FOREIGN KEY FK_D98D53CD943A5F0');
        $this->addSql('ALTER TABLE arbitre_categorie DROP FOREIGN KEY FK_D98D53CDBCF5E72D');
        $this->addSql('DROP TABLE arbitre_categorie');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE arbitre_categorie (arbitre_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_D98D53CD943A5F0 (arbitre_id), INDEX IDX_D98D53CDBCF5E72D (categorie_id), PRIMARY KEY(arbitre_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE arbitre_categorie ADD CONSTRAINT FK_D98D53CD943A5F0 FOREIGN KEY (arbitre_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE arbitre_categorie ADD CONSTRAINT FK_D98D53CDBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_tournoi DROP FOREIGN KEY FK_472C777DBCF5E72D');
        $this->addSql('ALTER TABLE categorie_tournoi DROP FOREIGN KEY FK_472C777DF607770A');
        $this->addSql('ALTER TABLE categorie_tournoi_arbitre DROP FOREIGN KEY FK_D0DD2CE368CEBDA6');
        $this->addSql('ALTER TABLE categorie_tournoi_arbitre DROP FOREIGN KEY FK_D0DD2CE3943A5F0');
        $this->addSql('DROP TABLE categorie_tournoi');
        $this->addSql('DROP TABLE categorie_tournoi_arbitre');
    }
}
