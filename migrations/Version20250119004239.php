<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250119004239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE reservation_vehicle_option (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, vehicle_options_id INT DEFAULT NULL, price_by_option NUMERIC(5, 2) DEFAULT NULL, count INT DEFAULT NULL, INDEX IDX_E66C405AB83297E7 (reservation_id), INDEX IDX_E66C405ABF498A6C (vehicle_options_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation_vehicle_option ADD CONSTRAINT FK_E66C405AB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation_vehicle_option ADD CONSTRAINT FK_E66C405ABF498A6C FOREIGN KEY (vehicle_options_id) REFERENCES vehicle_option (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation_vehicle_option DROP FOREIGN KEY FK_E66C405AB83297E7');
        $this->addSql('ALTER TABLE reservation_vehicle_option DROP FOREIGN KEY FK_E66C405ABF498A6C');
        $this->addSql('DROP TABLE reservation_vehicle_option');
    }
}
