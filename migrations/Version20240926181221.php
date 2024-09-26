<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926181221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE combat_combat (combat_source INT NOT NULL, combat_target INT NOT NULL, INDEX IDX_22D5DDF1F00E411A (combat_source), INDEX IDX_22D5DDF1E9EB1195 (combat_target), PRIMARY KEY(combat_source, combat_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE combat_combat ADD CONSTRAINT FK_22D5DDF1F00E411A FOREIGN KEY (combat_source) REFERENCES combat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE combat_combat ADD CONSTRAINT FK_22D5DDF1E9EB1195 FOREIGN KEY (combat_target) REFERENCES combat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adherant ADD combattant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adherant ADD CONSTRAINT FK_97DA58BC4B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97DA58BC4B0BCC96 ON adherant (combattant_id)');
        $this->addSql('ALTER TABLE arbitre ADD affectation_arbitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arbitre ADD CONSTRAINT FK_20B2E66E5CF908A2 FOREIGN KEY (affectation_arbitre_id) REFERENCES affectation_arbitre (id)');
        $this->addSql('CREATE INDEX IDX_20B2E66E5CF908A2 ON arbitre (affectation_arbitre_id)');
        $this->addSql('ALTER TABLE club ADD tournoi_id INT DEFAULT NULL, ADD combattant_id INT DEFAULT NULL, ADD adherant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE38724B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id)');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872BE612E45 FOREIGN KEY (adherant_id) REFERENCES adherant (id)');
        $this->addSql('CREATE INDEX IDX_B8EE3872F607770A ON club (tournoi_id)');
        $this->addSql('CREATE INDEX IDX_B8EE38724B0BCC96 ON club (combattant_id)');
        $this->addSql('CREATE INDEX IDX_B8EE3872BE612E45 ON club (adherant_id)');
        $this->addSql('ALTER TABLE combat ADD arbitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398943A5F0 FOREIGN KEY (arbitre_id) REFERENCES arbitre (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D51E398943A5F0 ON combat (arbitre_id)');
        $this->addSql('ALTER TABLE combattant ADD historique_combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combattant ADD CONSTRAINT FK_C3E38EF6AF0DA6F0 FOREIGN KEY (historique_combat_id) REFERENCES historique_combat (id)');
        $this->addSql('CREATE INDEX IDX_C3E38EF6AF0DA6F0 ON combattant (historique_combat_id)');
        $this->addSql('ALTER TABLE historique_combat ADD tournoi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE historique_combat ADD CONSTRAINT FK_441343C4F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_441343C4F607770A ON historique_combat (tournoi_id)');
        $this->addSql('ALTER TABLE inscription ADD tournoi_id INT DEFAULT NULL, ADD combattant_id INT DEFAULT NULL, ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D64B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D661190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E90F6D6F607770A ON inscription (tournoi_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E90F6D64B0BCC96 ON inscription (combattant_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E90F6D661190A32 ON inscription (club_id)');
        $this->addSql('ALTER TABLE tournoi ADD combat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DFFC7EEDB8 FOREIGN KEY (combat_id) REFERENCES combat (id)');
        $this->addSql('CREATE INDEX IDX_18AFD9DFFC7EEDB8 ON tournoi (combat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE combat_combat DROP FOREIGN KEY FK_22D5DDF1F00E411A');
        $this->addSql('ALTER TABLE combat_combat DROP FOREIGN KEY FK_22D5DDF1E9EB1195');
        $this->addSql('DROP TABLE combat_combat');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE adherant DROP FOREIGN KEY FK_97DA58BC4B0BCC96');
        $this->addSql('DROP INDEX UNIQ_97DA58BC4B0BCC96 ON adherant');
        $this->addSql('ALTER TABLE adherant DROP combattant_id');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DFFC7EEDB8');
        $this->addSql('DROP INDEX IDX_18AFD9DFFC7EEDB8 ON tournoi');
        $this->addSql('ALTER TABLE tournoi DROP combat_id');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872F607770A');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE38724B0BCC96');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872BE612E45');
        $this->addSql('DROP INDEX IDX_B8EE3872F607770A ON club');
        $this->addSql('DROP INDEX IDX_B8EE38724B0BCC96 ON club');
        $this->addSql('DROP INDEX IDX_B8EE3872BE612E45 ON club');
        $this->addSql('ALTER TABLE club DROP tournoi_id, DROP combattant_id, DROP adherant_id');
        $this->addSql('ALTER TABLE arbitre DROP FOREIGN KEY FK_20B2E66E5CF908A2');
        $this->addSql('DROP INDEX IDX_20B2E66E5CF908A2 ON arbitre');
        $this->addSql('ALTER TABLE arbitre DROP affectation_arbitre_id');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398943A5F0');
        $this->addSql('DROP INDEX UNIQ_8D51E398943A5F0 ON combat');
        $this->addSql('ALTER TABLE combat DROP arbitre_id');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6F607770A');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D64B0BCC96');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D661190A32');
        $this->addSql('DROP INDEX UNIQ_5E90F6D6F607770A ON inscription');
        $this->addSql('DROP INDEX UNIQ_5E90F6D64B0BCC96 ON inscription');
        $this->addSql('DROP INDEX UNIQ_5E90F6D661190A32 ON inscription');
        $this->addSql('ALTER TABLE inscription DROP tournoi_id, DROP combattant_id, DROP club_id');
        $this->addSql('ALTER TABLE historique_combat DROP FOREIGN KEY FK_441343C4F607770A');
        $this->addSql('DROP INDEX UNIQ_441343C4F607770A ON historique_combat');
        $this->addSql('ALTER TABLE historique_combat DROP tournoi_id');
        $this->addSql('ALTER TABLE combattant DROP FOREIGN KEY FK_C3E38EF6AF0DA6F0');
        $this->addSql('DROP INDEX IDX_C3E38EF6AF0DA6F0 ON combattant');
        $this->addSql('ALTER TABLE combattant DROP historique_combat_id');
    }
}
