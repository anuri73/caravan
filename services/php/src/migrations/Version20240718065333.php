<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718065333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE parameter_validation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE parameter_validation (id INT NOT NULL, parameter_name VARCHAR(255) NOT NULL, constraint_class VARCHAR(512) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_333ADD63F12D7D03 ON parameter_validation (parameter_name)');
        $this->addSql('ALTER TABLE parameter_validation ADD CONSTRAINT FK_333ADD63F12D7D03 FOREIGN KEY (parameter_name) REFERENCES parameter (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parameter ADD post_form VARCHAR(255) DEFAULT \'textbox\' NOT NULL');
        $this->addSql('ALTER TABLE parameter ADD search_form VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE parameter ADD search_priority INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE parameter_validation_id_seq CASCADE');
        $this->addSql('ALTER TABLE parameter_validation DROP CONSTRAINT FK_333ADD63F12D7D03');
        $this->addSql('DROP TABLE parameter_validation');
        $this->addSql('ALTER TABLE parameter DROP post_form');
        $this->addSql('ALTER TABLE parameter DROP search_form');
        $this->addSql('ALTER TABLE parameter DROP search_priority');
    }
}
