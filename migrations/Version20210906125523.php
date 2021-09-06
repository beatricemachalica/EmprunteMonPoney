<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210906125523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_post (activity_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_594EB71F81C06096 (activity_id), INDEX IDX_594EB71F4B89032C (post_id), PRIMARY KEY(activity_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_post ADD CONSTRAINT FK_594EB71F81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_post ADD CONSTRAINT FK_594EB71F4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE activity_equid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_equid (activity_id INT NOT NULL, equid_id INT NOT NULL, INDEX IDX_F6AB217E81C06096 (activity_id), INDEX IDX_F6AB217E55BF5F11 (equid_id), PRIMARY KEY(activity_id, equid_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE activity_equid ADD CONSTRAINT FK_F6AB217E55BF5F11 FOREIGN KEY (equid_id) REFERENCES equid (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_equid ADD CONSTRAINT FK_F6AB217E81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_post');
    }
}
