<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260418161600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD points INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE "user" ADD total_commandes INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP NOT NULL DEFAULT NOW()');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP points');
        $this->addSql('ALTER TABLE "user" DROP total_commandes');
        $this->addSql('ALTER TABLE "user" DROP created_at');
    }
}
