<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191209175013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE helpdesk_messages (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message LONGTEXT NOT NULL, answer LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, answered DATETIME DEFAULT NULL, email VARCHAR(100) NOT NULL, INDEX IDX_D111C903A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE helpdesk_messages ADD CONSTRAINT FK_D111C903A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('INSERT INTO roles (role, name) VALUES (\'ROLE_HELP_DESK\', \'Správce helpdesku\')');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE helpdesk_messages');
        $this->addSql('DELETE FROM roles WHERE role=\'ROLE_HELP_DESK\'');
    }
}
