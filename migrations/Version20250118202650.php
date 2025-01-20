<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118202650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motorcycle DROP FOREIGN KEY FK_21E380E1C54C8C93');
        $this->addSql('DROP INDEX IDX_21E380E1C54C8C93 ON motorcycle');
        $this->addSql('ALTER TABLE motorcycle ADD type VARCHAR(100) NOT NULL, CHANGE type_id motorcycle_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motorcycle ADD CONSTRAINT FK_21E380E11A19CD7B FOREIGN KEY (motorcycle_type_id) REFERENCES motorcycle_type (id)');
        $this->addSql('CREATE INDEX IDX_21E380E11A19CD7B ON motorcycle (motorcycle_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE motorcycle DROP FOREIGN KEY FK_21E380E11A19CD7B');
        $this->addSql('DROP INDEX IDX_21E380E11A19CD7B ON motorcycle');
        $this->addSql('ALTER TABLE motorcycle DROP type, CHANGE motorcycle_type_id type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE motorcycle ADD CONSTRAINT FK_21E380E1C54C8C93 FOREIGN KEY (type_id) REFERENCES motorcycle_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_21E380E1C54C8C93 ON motorcycle (type_id)');
    }
}
