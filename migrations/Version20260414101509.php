<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414101509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etablissement_filiere (etablissement_id INT NOT NULL, filiere_id INT NOT NULL, PRIMARY KEY (etablissement_id, filiere_id))');
        $this->addSql('CREATE INDEX IDX_2AC1425DFF631228 ON etablissement_filiere (etablissement_id)');
        $this->addSql('CREATE INDEX IDX_2AC1425D180AA129 ON etablissement_filiere (filiere_id)');
        $this->addSql('ALTER TABLE etablissement_filiere ADD CONSTRAINT FK_2AC1425DFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etablissement_filiere ADD CONSTRAINT FK_2AC1425D180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE filiere_etablissement DROP CONSTRAINT fk_6707593e180aa129');
        $this->addSql('ALTER TABLE filiere_etablissement DROP CONSTRAINT fk_6707593eff631228');
        $this->addSql('DROP TABLE filiere_etablissement');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE filiere_etablissement (filiere_id INT NOT NULL, etablissement_id INT NOT NULL, PRIMARY KEY (filiere_id, etablissement_id))');
        $this->addSql('CREATE INDEX idx_6707593e180aa129 ON filiere_etablissement (filiere_id)');
        $this->addSql('CREATE INDEX idx_6707593eff631228 ON filiere_etablissement (etablissement_id)');
        $this->addSql('ALTER TABLE filiere_etablissement ADD CONSTRAINT fk_6707593e180aa129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE filiere_etablissement ADD CONSTRAINT fk_6707593eff631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etablissement_filiere DROP CONSTRAINT FK_2AC1425DFF631228');
        $this->addSql('ALTER TABLE etablissement_filiere DROP CONSTRAINT FK_2AC1425D180AA129');
        $this->addSql('DROP TABLE etablissement_filiere');
    }
}
