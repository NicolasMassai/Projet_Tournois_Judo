<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241002130129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE club ADD president_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE club ADD CONSTRAINT FK_B8EE3872B40A33C7 FOREIGN KEY (president_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8EE3872B40A33C7 ON club (president_id)');
        $this->addSql('ALTER TABLE combat ADD CONSTRAINT FK_8D51E398943A5F0 FOREIGN KEY (arbitre_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE club DROP FOREIGN KEY FK_B8EE3872B40A33C7');
        $this->addSql('DROP INDEX UNIQ_B8EE3872B40A33C7 ON club');
        $this->addSql('ALTER TABLE club DROP president_id');
        $this->addSql('ALTER TABLE combat DROP FOREIGN KEY FK_8D51E398943A5F0');
    }
}
