<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721121603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSQL('SET FOREIGN_KEY_CHECKS=0');
        $this->addSql('ALTER TABLE bi_project ADD last_resource_id INT UNSIGNED NOT NULL DEFAULT 0, DROP short_id, CHANGE id id VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE bi_project_user CHANGE project_id project_id VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE bi_task CHANGE project_id project_id VARCHAR(12) DEFAULT NULL');
        $this->addSQL('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bi_project ADD short_id VARCHAR(12) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP last_resource_id, CHANGE id id VARCHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE bi_project_user CHANGE project_id project_id VARCHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE bi_task CHANGE project_id project_id VARCHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
