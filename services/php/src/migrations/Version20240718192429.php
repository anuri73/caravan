<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718192429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE parameter_validation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE parameter (name VARCHAR(255) NOT NULL, category_name VARCHAR(255) NOT NULL, parent_name VARCHAR(255) DEFAULT NULL, parent_category_name VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, post_form VARCHAR(255) DEFAULT \'textbox\' NOT NULL, search_form VARCHAR(255) DEFAULT NULL, search_priority INT DEFAULT NULL, PRIMARY KEY(name, category_name))');
        $this->addSql('CREATE INDEX IDX_2A979110D5B80441 ON parameter (category_name)');
        $this->addSql('CREATE INDEX IDX_2A97911063C048B293E67D3 ON parameter (parent_name, parent_category_name)');
        $this->addSql('CREATE UNIQUE INDEX idx_parameter_name ON parameter (name, category_name)');
        $this->addSql('COMMENT ON COLUMN parameter.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN parameter.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE parameter_validation (id INT NOT NULL, parameter_name VARCHAR(255) NOT NULL, parameter_category_name VARCHAR(255) NOT NULL, constraint_class VARCHAR(512) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_333ADD63F12D7D03A9D59E8B ON parameter_validation (parameter_name, parameter_category_name)');
        $this->addSql('CREATE TABLE post (id UUID NOT NULL, author_id INT NOT NULL, title VARCHAR(1024) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE post_parameter_value (post_id UUID NOT NULL, parameter_name VARCHAR(255) NOT NULL, parameter_category_name VARCHAR(255) NOT NULL, PRIMARY KEY(post_id, parameter_name, parameter_category_name))');
        $this->addSql('CREATE INDEX IDX_208462D04B89032C ON post_parameter_value (post_id)');
        $this->addSql('CREATE INDEX IDX_208462D0F12D7D03A9D59E8B ON post_parameter_value (parameter_name, parameter_category_name)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(512) NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A979110D5B80441 FOREIGN KEY (category_name) REFERENCES category (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A97911063C048B293E67D3 FOREIGN KEY (parent_name, parent_category_name) REFERENCES parameter (name, category_name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameter_validation ADD CONSTRAINT FK_333ADD63F12D7D03A9D59E8B FOREIGN KEY (parameter_name, parameter_category_name) REFERENCES parameter (name, category_name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_parameter_value ADD CONSTRAINT FK_208462D04B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_parameter_value ADD CONSTRAINT FK_208462D0F12D7D03A9D59E8B FOREIGN KEY (parameter_name, parameter_category_name) REFERENCES parameter (name, category_name) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE parameter_validation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE parameter DROP CONSTRAINT FK_2A979110D5B80441');
        $this->addSql('ALTER TABLE parameter DROP CONSTRAINT FK_2A97911063C048B293E67D3');
        $this->addSql('ALTER TABLE parameter_validation DROP CONSTRAINT FK_333ADD63F12D7D03A9D59E8B');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_parameter_value DROP CONSTRAINT FK_208462D04B89032C');
        $this->addSql('ALTER TABLE post_parameter_value DROP CONSTRAINT FK_208462D0F12D7D03A9D59E8B');
        $this->addSql('DROP TABLE parameter');
        $this->addSql('DROP TABLE parameter_validation');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_parameter_value');
        $this->addSql('DROP TABLE "user"');
    }
}
