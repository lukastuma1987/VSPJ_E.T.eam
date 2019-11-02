<?php

namespace FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Logos Polytechnikos', $client->getResponse()->getContent());
    }

    public function testCislaCasopisu()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cisla-casopisu');

        $this->assertContains('Čísla časopisu', $client->getResponse()->getContent());
    }
}
