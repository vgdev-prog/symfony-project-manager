<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112014341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_users (id VARCHAR(255) NOT NULL, date DATE NOT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, user_status VARCHAR(255) NOT NULL, user_role VARCHAR(255) DEFAULT NULL, reset_token_token VARCHAR(255) NOT NULL, reset_token_expires_at DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_users.date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE users_user_networks (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, network VARCHAR(255) NOT NULL, identity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_30D15826A76ED395 ON users_user_networks (user_id)');
        $this->addSql('COMMENT ON COLUMN users_user_networks.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users_user_networks.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE users_user_networks ADD CONSTRAINT FK_30D15826A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users_user_networks DROP CONSTRAINT FK_30D15826A76ED395');
        $this->addSql('DROP TABLE user_users');
        $this->addSql('DROP TABLE users_user_networks');
    }
}
