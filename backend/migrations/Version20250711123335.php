<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711123335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revenue (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, category VARCHAR(100) NOT NULL, date DATE NOT NULL, payment_method VARCHAR(100) DEFAULT NULL, customer VARCHAR(255) DEFAULT NULL, invoice_number VARCHAR(100) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_E9116C85A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE revenue ADD CONSTRAINT FK_E9116C85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revenue DROP FOREIGN KEY FK_E9116C85A76ED395');
        $this->addSql('DROP TABLE revenue');
    }
}
