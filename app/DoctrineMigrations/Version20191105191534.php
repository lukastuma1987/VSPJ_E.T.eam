<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191105191534 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE article_versions DROP file');
        $this->addSql('ALTER TABLE magazines DROP file');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE article_versions ADD file LONGBLOB NOT NULL');
        $this->addSql('ALTER TABLE magazines ADD file LONGBLOB DEFAULT NULL');
    }
}
