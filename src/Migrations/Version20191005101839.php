<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191005101839 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE bi_project SET id = UPPER(REPLACE(name, ' ', '')) where id LIKE \"%-%\"");
    }

    public function down(Schema $schema) : void
    {
        // No need to revert changes on down at this stage of bear-it.
    }
}
