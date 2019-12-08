<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191205130712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE articles SET status=5 WHERE status=2');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('UPDATE articles SET status=2 WHERE status=5');
    }
}
