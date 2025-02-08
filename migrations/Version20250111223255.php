<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250111223255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification CHANGE type type VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE review CHANGE rating rating DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE vehicle_option ADD price NUMERIC(5, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicle_option DROP price');
        $this->addSql('ALTER TABLE notification CHANGE type type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE review CHANGE rating rating INT NOT NULL');
    }
}
