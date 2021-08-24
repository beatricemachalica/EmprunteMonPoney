<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210823210242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equid ADD CONSTRAINT FK_E8A917CDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E8A917CDA76ED395 ON equid (user_id)');
        $this->addSql('ALTER TABLE post ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494B89032C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64955BF5F11');
        $this->addSql('DROP INDEX UNIQ_8D93D6494B89032C ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64955BF5F11 ON user');
        $this->addSql('ALTER TABLE user DROP post_id, DROP equid_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_user ADD CONSTRAINT FK_44C6B1424B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_user ADD CONSTRAINT FK_44C6B142A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equid DROP FOREIGN KEY FK_E8A917CDA76ED395');
        $this->addSql('DROP INDEX IDX_E8A917CDA76ED395 ON equid');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post DROP user_id');
        $this->addSql('ALTER TABLE user ADD post_id INT DEFAULT NULL, ADD equid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64955BF5F11 FOREIGN KEY (equid_id) REFERENCES equid (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494B89032C ON user (post_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64955BF5F11 ON user (equid_id)');
    }
}
