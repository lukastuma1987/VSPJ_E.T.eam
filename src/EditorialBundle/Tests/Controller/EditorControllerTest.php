<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Repository\UserRepository;
use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditorControllerTest extends WebTestCase
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

    public function testAssignArticle()
    {
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        /** @var UserRepository $repository */
        $repository = $em->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneBy(['username' => 'author']);

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $article = new Article();
        $article->setOwner($user)->setName('Foo article');

        $magazine = new Magazine();
        $magazine->setDeadlineDate($tomorrow)
            ->setPublishDate($tomorrow)
            ->setNumber(1)
            ->setYear(1)
            ->addArticle($article)
        ;

        $em->persist($magazine);
        $em->flush();

        self::login($this->client, 'editor', 'editor');

        $crawler = $this->client->request('GET', '/redakce/redaktor/vypis-neprirazenych-clanku');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo article', $response->getContent());


        $form = $crawler->filter('form button')->form();

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirection());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $this->assertContains('Článek Vám byl úspěšně přiřazen. Recenzní řízení začalo.', $response->getContent());
    }
}
