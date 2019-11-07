<?php

namespace FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
}
