<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Repository\UserRepository;
use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class ArticleControllerTest extends WebTestCase
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

    /**
     * @dataProvider provideDetail
     */
    public function testDetailAndAddComment($username)
    {
        $article = $this->prepareArticle();

        self::login($this->client, $username, $username);

        $crawler = $this->client->request('GET', sprintf('/redakce/clanek/%d/detail', $article->getId()));
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo article', $response->getContent());

        $uri = $crawler->selectLink('Přidat komentář')->link()->getUri();

        $crawler = $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_articlecomment[content]'] = 'Foo Comment';

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirection());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo Comment', $response->getContent());
    }

    public function provideDetail()
    {
        return [
            ['author'],
            ['editor'],
            ['reviewer'],
        ];
    }

    // private

    private function prepareArticle()
    {
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        /** @var UserRepository $repository */
        $repository = $em->getRepository(User::class);
        /** @var User $author */
        $author = $repository->findOneBy(['username' => 'author']);
        /** @var User $editor */
        $editor = $repository->findOneBy(['username' => 'editor']);
        /** @var User $reviewer */
        $reviewer = $repository->findOneBy(['username' => 'reviewer']);

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $review = new Review();
        $review
            ->setReviewer($reviewer)
            ->setDeadline($tomorrow)
            ->setReview('Foo')
            ->setFilled(new \DateTime())
            ->setBenefitLevel(1)
            ->setLanguageLevel(1)
            ->setOriginalityLevel(1)
            ->setProfessionalLevel(1)
        ;

        $article = new Article();
        $article
            ->setOwner($author)
            ->setEditor($editor)
            ->setName('Foo article')
            ->setStatus(ArticleStatus::STATUS_REVIEWERS_ASSIGNED)
            ->addReview($review)
        ;

        $magazine = new Magazine();
        $magazine->setDeadlineDate($tomorrow)
            ->setPublishDate($tomorrow)
            ->setNumber(1)
            ->setYear(1)
            ->addArticle($article)
        ;

        $em->persist($magazine);
        $em->flush();

        return $article;
    }
}
