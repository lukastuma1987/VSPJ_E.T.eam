<?php

namespace FrontBundle\Tests\Controller;

use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class DefaultControllerTest extends WebTestCase
{
    /** @var Client */
    private $client;

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

    /**
     * @dataProvider providePageContains
     */
    public function testPageContains($uri, $expectedContent)
    {
        $crawler = $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains($expectedContent, $response->getContent());
    }

    public function providePageContains()
    {
        return [
            ['/', 'Logos Polytechnikos'],
            ['/cisla-casopisu', 'Čísla časopisu'],
            ['/o-casopise', 'vysokoškolský odborný recenzovaný časopis'],
        ];
    }

    /**
     * @dataProvider provideHelpDesk
     */
    public function testHelpDesk($username, $expectedEmail)
    {
        if ($username) {
            self::login($this->client, $username, $username);
        }

        $crawler = $this->client->request('GET', '/helpdesk');
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        if ($expectedEmail) {
            $this->assertContains($expectedEmail, $response->getContent());
        }

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_helpdeskmessage[email]'] = 'foo@foo.cz';
        $form['editorialbundle_helpdeskmessage[message]'] = 'Foo';

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Vaše zpráva byla úspěšně odeslána', $response->getContent());
    }

    public function provideHelpDesk()
    {
        return [
            [null, ''],
            ['author', 'author@author.cz'],
            ['reviewer', 'reviewer@reviewer.cz'],
            ['editor', 'editor@editor.cz'],
            ['chiefeditor', 'chiefeditor@chiefeditor.cz'],
            ['admin', 'admin@admin.cz'],
        ];
    }
}
