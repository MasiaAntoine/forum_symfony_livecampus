<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411084633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subject ADD name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject DROP name');
        $this->addSql('ALTER TABLE category DROP name');
        $this->addSql('ALTER TABLE board DROP name');
    }
}
