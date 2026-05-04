<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419081533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE commande_ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE commande_ingredient (id INT NOT NULL, commande_id INT DEFAULT NULL, plat_id INT DEFAULT NULL, ingredient_id INT DEFAULT NULL, replaced_by_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, quantity INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F36D549682EA2E54 ON commande_ingredient (commande_id)');
        $this->addSql('CREATE INDEX IDX_F36D5496D73DB560 ON commande_ingredient (plat_id)');
        $this->addSql('CREATE INDEX IDX_F36D5496933FE08C ON commande_ingredient (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_F36D54969AC69B54 ON commande_ingredient (replaced_by_id)');
        $this->addSql('ALTER TABLE commande_ingredient ADD CONSTRAINT FK_F36D549682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande_ingredient ADD CONSTRAINT FK_F36D5496D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande_ingredient ADD CONSTRAINT FK_F36D5496933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande_ingredient ADD CONSTRAINT FK_F36D54969AC69B54 FOREIGN KEY (replaced_by_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE commande_ingredient_id_seq CASCADE');
        $this->addSql('ALTER TABLE commande_ingredient DROP CONSTRAINT FK_F36D549682EA2E54');
        $this->addSql('ALTER TABLE commande_ingredient DROP CONSTRAINT FK_F36D5496D73DB560');
        $this->addSql('ALTER TABLE commande_ingredient DROP CONSTRAINT FK_F36D5496933FE08C');
        $this->addSql('ALTER TABLE commande_ingredient DROP CONSTRAINT FK_F36D54969AC69B54');
        $this->addSql('DROP TABLE commande_ingredient');
    }
}
