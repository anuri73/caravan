<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610115955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_parent (category_name VARCHAR(255) NOT NULL, parent_name VARCHAR(255) NOT NULL, PRIMARY KEY(category_name, parent_name))');
        $this->addSql('CREATE INDEX IDX_3266AA34D5B80441 ON category_parent (category_name)');
        $this->addSql('CREATE INDEX IDX_3266AA3463C048B2 ON category_parent (parent_name)');
        $this->addSql('ALTER TABLE category_parent ADD CONSTRAINT FK_3266AA34D5B80441 FOREIGN KEY (category_name) REFERENCES category (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_parent ADD CONSTRAINT FK_3266AA3463C048B2 FOREIGN KEY (parent_name) REFERENCES category (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c19981b510');
        $this->addSql('DROP INDEX idx_64c19c19981b510');
        $this->addSql('ALTER TABLE category DROP parent_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category_parent DROP CONSTRAINT FK_3266AA34D5B80441');
        $this->addSql('ALTER TABLE category_parent DROP CONSTRAINT FK_3266AA3463C048B2');
        $this->addSql('DROP TABLE category_parent');
        $this->addSql('ALTER TABLE category ADD parent_category VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c19981b510 FOREIGN KEY (parent_category) REFERENCES category (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_64c19c19981b510 ON category (parent_category)');
    }
}
