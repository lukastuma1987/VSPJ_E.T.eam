<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class SecurityControllerTest extends WebTestCase
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

    public function testLoginRedirect()
    {
        $crawler = $this->client->request('GET', '/redakce/');

        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    /**
     * @dataProvider provideLogin
     */
    public function testLogin($username, $password)
    {
        $crawler = $this->client->request('GET', '/login');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $form = $crawler->filter('form button')->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirection());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $this->assertContains('Nadcházející čísla časopisu', $response->getContent());
    }

    public function provideLogin()
    {
        return [
            ['admin', 'admin'],
            ['author', 'author'],
            ['reviewer', 'reviewer'],
            ['editor', 'editor'],
            ['chiefeditor', 'chiefeditor'],
        ];
    }
}
