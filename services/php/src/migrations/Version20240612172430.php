<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612172430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parameter (name VARCHAR(255) NOT NULL, category_name VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE INDEX IDX_2A979110D5B80441 ON parameter (category_name)');
        $this->addSql('CREATE UNIQUE INDEX idx_parameter_name ON parameter (name)');
        $this->addSql('COMMENT ON COLUMN parameter.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN parameter.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A979110D5B80441 FOREIGN KEY (category_name) REFERENCES category (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE parameter DROP CONSTRAINT FK_2A979110D5B80441');
        $this->addSql('DROP TABLE parameter');
    }
}
