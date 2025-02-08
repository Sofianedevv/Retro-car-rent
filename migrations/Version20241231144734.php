<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241231144734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE van ADD nb_seats INT NOT NULL, ADD nb_doors INT NOT NULL, CHANGE cargo_volume cargo_volume DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE van DROP nb_seats, DROP nb_doors, CHANGE cargo_volume cargo_volume INT NOT NULL');
    }
}
