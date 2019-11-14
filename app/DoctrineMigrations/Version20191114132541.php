<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191114132541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE article_comment (id BIGINT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_79A616DB7294869C (article_id), INDEX IDX_79A616DBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DB7294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE article_comment ADD CONSTRAINT FK_79A616DBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE article_comment');
    }
}
