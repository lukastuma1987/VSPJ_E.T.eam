<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Repository\UserRepository;
use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

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
        $this->prepareArticle();

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

    public function testAssignReviewers()
    {
        $this->prepareArticle(ArticleStatus::STATUS_ASSIGNED, true);

        self::login($this->client, 'editor', 'editor');

        $crawler = $this->client->request('GET', '/redakce/redaktor/vypis-clanku-prirazenych-mne');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo article', $response->getContent());

        $uri = $crawler->selectLink('Vybrat recenzenty')->link()->getUri();

        $crawler = $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_assignreviewers[reviewers]']->select([2, 6]);

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirection());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Článek byl předán k hodnocení', $response->getContent());
    }

    // private

    private function prepareArticle($articleStatus = false, $hasEditor = false)
    {
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        /** @var UserRepository $repository */
        $repository = $em->getRepository(User::class);
        /** @var User $author */
        $author = $repository->findOneBy(['username' => 'author']);
        /** @var User $editor */
        $editor = $repository->findOneBy(['username' => 'editor']);

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $article = new Article();
        $article->setOwner($author)->setName('Foo article');

        if ($articleStatus !== false) {
            $article->setStatus($articleStatus);
        }

        if ($hasEditor) {
            $article->setEditor($editor);
        }

        $magazine = new Magazine();
        $magazine->setDeadlineDate($tomorrow)
            ->setPublishDate($tomorrow)
            ->setNumber(1)
            ->setYear(1)
            ->addArticle($article)
        ;

        $em->persist($magazine);
        $em->flush();
    }
}
