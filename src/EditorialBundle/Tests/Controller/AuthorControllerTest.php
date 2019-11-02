<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthorControllerTest extends WebTestCase
{
    /** @var Client $client  */
    private $client;

    public static function setUpBeforeClass()
    {
        self::loadFixtures();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->client = null;
        parent::tearDown();
    }

    public function testCreateArticle()
    {
        self::login($this->client, 'author', 'author');

        $crawler = $this->client->request('GET', '/redakce/autor/novy-clanek');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $file = new UploadedFile(__DIR__ .'/../Fixtures/fixtures.sql', 'fixtures.sql', 'application/sql', 123);

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_article[name]'] = 'Foo';
        $form['editorialbundle_article[magazine]']->select(1);
        $form['editorialbundle_article[file]'] = $file;

        $form['editorialbundle_article[authors][0][fullName]'] = 'Foo';
        $form['editorialbundle_article[authors][0][email]'] = 'Foo@Foo.cz';
        $form['editorialbundle_article[authors][0][workplace]'] = 'Foo';
        $form['editorialbundle_article[authors][0][address]'] = 'Foo';

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirection());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $this->assertContains('Článek byl úspěšně vytvořen.', $response->getContent());
    }
}
