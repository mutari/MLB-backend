<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210209201229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D154BB0713B');
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D15A0BDB2F3');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D154BB0713B FOREIGN KEY (play_list_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D15A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D15A0BDB2F3');
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D154BB0713B');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D15A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D154BB0713B FOREIGN KEY (play_list_id) REFERENCES playlist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
