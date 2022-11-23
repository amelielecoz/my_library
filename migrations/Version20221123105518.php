<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123105518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reservation_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reservation_request (id INT NOT NULL, book_id INT NOT NULL, requestor_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C02341A16A2B381 ON reservation_request (book_id)');
        $this->addSql('COMMENT ON COLUMN reservation_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation_request ADD CONSTRAINT FK_5C02341A16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reservation_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE reservation_request DROP CONSTRAINT FK_5C02341A16A2B381');
        $this->addSql('DROP TABLE reservation_request');
    }
}
