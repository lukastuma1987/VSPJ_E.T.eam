<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191109125721 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE reviews ADD benefit_level INT DEFAULT NULL, ADD originality_level INT DEFAULT NULL, ADD professional_level INT DEFAULT NULL, ADD language_level INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE reviews DROP benefit_level, DROP originality_level, DROP professional_level, DROP language_level');
    }
}
