<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191101110009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, magazine_id INT DEFAULT NULL, user_id INT DEFAULT NULL, editor_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_BFDD31683EB84A1D (magazine_id), INDEX IDX_BFDD3168A76ED395 (user_id), INDEX IDX_BFDD31686995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_users (article_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FC618D1D7294869C (article_id), INDEX IDX_FC618D1DA76ED395 (user_id), PRIMARY KEY(article_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_authors (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, fullName VARCHAR(255) NOT NULL, email VARCHAR(100) NOT NULL, workplace VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_63DE6EE67294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_versions (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, file LONGBLOB NOT NULL, created DATETIME NOT NULL, INDEX IDX_4C34B4B97294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE magazines (id INT AUTO_INCREMENT NOT NULL, publishDate DATETIME NOT NULL, deadlineDate DATETIME NOT NULL, year INT NOT NULL, number INT NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE magazine_topics (id INT AUTO_INCREMENT NOT NULL, magazine_id INT DEFAULT NULL, topic VARCHAR(255) NOT NULL, INDEX IDX_703A09D63EB84A1D (magazine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31683EB84A1D FOREIGN KEY (magazine_id) REFERENCES magazines (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31686995AC4C FOREIGN KEY (editor_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1D7294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_authors ADD CONSTRAINT FK_63DE6EE67294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE article_versions ADD CONSTRAINT FK_4C34B4B97294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE magazine_topics ADD CONSTRAINT FK_703A09D63EB84A1D FOREIGN KEY (magazine_id) REFERENCES magazines (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE articles_users DROP FOREIGN KEY FK_FC618D1D7294869C');
        $this->addSql('ALTER TABLE article_authors DROP FOREIGN KEY FK_63DE6EE67294869C');
        $this->addSql('ALTER TABLE article_versions DROP FOREIGN KEY FK_4C34B4B97294869C');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31683EB84A1D');
        $this->addSql('ALTER TABLE magazine_topics DROP FOREIGN KEY FK_703A09D63EB84A1D');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_users');
        $this->addSql('DROP TABLE article_authors');
        $this->addSql('DROP TABLE article_versions');
        $this->addSql('DROP TABLE magazines');
        $this->addSql('DROP TABLE magazine_topics');
    }
}
