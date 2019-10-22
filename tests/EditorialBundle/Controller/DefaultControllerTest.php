<?php

namespace EditorialBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/redakce/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
