<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928084035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherant ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adherant ADD CONSTRAINT FK_97DA58BC61190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_97DA58BC61190A32 ON adherant (club_id)');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE38724B0BCC96');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872BE612E45');
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872F607770A');
        $this->addSql('DROP INDEX IDX_B8EE3872F607770A ON club');
        $this->addSql('DROP INDEX IDX_B8EE38724B0BCC96 ON club');
        $this->addSql('DROP INDEX IDX_B8EE3872BE612E45 ON club');
        $this->addSql('ALTER TABLE club DROP tournoi_id, DROP combattant_id, DROP adherant_id');
        $this->addSql('ALTER TABLE combattant ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE combattant ADD CONSTRAINT FK_C3E38EF661190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_C3E38EF661190A32 ON combattant (club_id)');
        $this->addSql('ALTER TABLE tournoi ADD club_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournoi ADD CONSTRAINT FK_18AFD9DF61190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_18AFD9DF61190A32 ON tournoi (club_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherant DROP FOREIGN KEY FK_97DA58BC61190A32');
        $this->addSql('DROP INDEX IDX_97DA58BC61190A32 ON adherant');
        $this->addSql('ALTER TABLE adherant DROP club_id');
        $this->addSql('ALTER TABLE club ADD tournoi_id INT DEFAULT NULL, ADD combattant_id INT DEFAULT NULL, ADD adherant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE38724B0BCC96 FOREIGN KEY (combattant_id) REFERENCES combattant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872BE612E45 FOREIGN KEY (adherant_id) REFERENCES adherant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B8EE3872F607770A ON club (tournoi_id)');
        $this->addSql('CREATE INDEX IDX_B8EE38724B0BCC96 ON club (combattant_id)');
        $this->addSql('CREATE INDEX IDX_B8EE3872BE612E45 ON club (adherant_id)');
        $this->addSql('ALTER TABLE tournoi DROP FOREIGN KEY FK_18AFD9DF61190A32');
        $this->addSql('DROP INDEX IDX_18AFD9DF61190A32 ON tournoi');
        $this->addSql('ALTER TABLE tournoi DROP club_id');
        $this->addSql('ALTER TABLE combattant DROP FOREIGN KEY FK_C3E38EF661190A32');
        $this->addSql('DROP INDEX IDX_C3E38EF661190A32 ON combattant');
        $this->addSql('ALTER TABLE combattant DROP club_id');
    }
}
