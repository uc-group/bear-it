<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721142507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('bi_project_resource')) {
            return;
        }
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bi_project_resource (resource_number INT UNSIGNED NOT NULL, resource_type VARCHAR(255) NOT NULL, project_id VARCHAR(12) NOT NULL, INDEX IDX_3B097EDB166D1F9C (project_id), PRIMARY KEY(project_id, resource_number, resource_type)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bi_project_resource ADD CONSTRAINT FK_3B097EDB166D1F9C FOREIGN KEY (project_id) REFERENCES bi_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bi_project CHANGE last_resource_id last_resource_id INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bi_project_resource');
        $this->addSql('ALTER TABLE bi_project CHANGE last_resource_id last_resource_id INT UNSIGNED DEFAULT 0 NOT NULL');
    }
}
