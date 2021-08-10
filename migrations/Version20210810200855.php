<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210810200855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equid ADD breed VARCHAR(55) DEFAULT NULL, DROP race');
        $this->addSql('ALTER TABLE user ADD pseudo VARCHAR(155) NOT NULL, ADD is_verified TINYINT(1) NOT NULL, ADD register_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ADD departement VARCHAR(55) NOT NULL, ADD cp VARCHAR(15) NOT NULL, ADD city VARCHAR(75) NOT NULL, ADD phone_number VARCHAR(25) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equid ADD race VARCHAR(155) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP breed');
        $this->addSql('ALTER TABLE user DROP pseudo, DROP is_verified, DROP register_date, DROP departement, DROP cp, DROP city, DROP phone_number');
    }
}
