<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210206165338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D156BBD148');
        $this->addSql('DROP INDEX IDX_41301D156BBD148 ON play_list_song');
        $this->addSql('ALTER TABLE play_list_song CHANGE playlist_id play_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D154BB0713B FOREIGN KEY (play_list_id) REFERENCES playlist (id)');
        $this->addSql('CREATE INDEX IDX_41301D154BB0713B ON play_list_song (play_list_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE play_list_song DROP FOREIGN KEY FK_41301D154BB0713B');
        $this->addSql('DROP INDEX IDX_41301D154BB0713B ON play_list_song');
        $this->addSql('ALTER TABLE play_list_song CHANGE play_list_id playlist_id INT NOT NULL');
        $this->addSql('ALTER TABLE play_list_song ADD CONSTRAINT FK_41301D156BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_41301D156BBD148 ON play_list_song (playlist_id)');
    }
}
