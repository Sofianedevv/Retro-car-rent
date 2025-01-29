<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250128205854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql(sql: 'CREATE TABLE motorcycle_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE motorcycle ADD motorcycle_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motorcycle ADD CONSTRAINT FK_21E380E11A19CD7B FOREIGN KEY (motorcycle_type_id) REFERENCES motorcycle_type (id)');
        $this->addSql('CREATE INDEX IDX_21E380E11A19CD7B ON motorcycle (motorcycle_type_id)');
        $this->addSql('ALTER TABLE reservation ADD reference CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955AEA34913 ON reservation (reference)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE motorcycle DROP FOREIGN KEY FK_21E380E11A19CD7B');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE motorcycle_type');
        $this->addSql('DROP INDEX IDX_21E380E11A19CD7B ON motorcycle');
        $this->addSql('ALTER TABLE motorcycle DROP motorcycle_type_id');
        $this->addSql('DROP INDEX UNIQ_42C84955AEA34913 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP reference');
    }
}
