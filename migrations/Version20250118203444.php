<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118203444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vehicle_category (vehicle_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DB5E1655545317D1 (vehicle_id), INDEX IDX_DB5E165512469DE2 (category_id), PRIMARY KEY(vehicle_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle_category ADD CONSTRAINT FK_DB5E1655545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicle_category ADD CONSTRAINT FK_DB5E165512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicle_category DROP FOREIGN KEY FK_DB5E1655545317D1');
        $this->addSql('ALTER TABLE vehicle_category DROP FOREIGN KEY FK_DB5E165512469DE2');
        $this->addSql('DROP TABLE vehicle_category');
    }
}
