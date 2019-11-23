<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleAuthor;
use EditorialBundle\Entity\ArticleVersion;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\Review;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Repository\UserRepository;
use EditorialBundle\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DownloadControllerTest extends WebTestCase
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
     * @dataProvider provideDownloadArticle
     */
    public function testDownloadArticle($username, $expectedStatusCode)
    {
        $article = $this->prepareArticle();

        self::login($this->client, $username, $username);

        $uri = sprintf('/redakce/stahnout/clanek/%d', $article->getId());
        $crawler = $this->client->request('GET', $uri);

        $response = $this->client->getResponse();

        $this->assertSame($expectedStatusCode, $response->getStatusCode());

        if ($expectedStatusCode === 200) {
            $this->assertSame('application/octet-stream', $response->headers->get('Content-Type'));
        } else {
            $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
        }
    }

    public function provideDownloadArticle()
    {
        return [
            ['author', 200],
            ['reviewer', 200],
            ['editor', 200],
            ['chiefeditor', 200],
            ['admin', 403],
            ['reviewer2', 403],
        ];
    }

    /**
     * @dataProvider provideDownloadArticleVersionAction
     */
    public function testDownloadArticleVersionAction($username, $expectedStatusCode)
    {
        $article = $this->prepareArticle();
        $version1 = $article->getVersions()[0];
        $version2 = $article->getVersions()[1];

        self::login($this->client, $username, $username);

        $uri1 = sprintf('/redakce/stahnout/clanek/%d/verze/%d', $article->getId(), $version1->getId());
        $uri2 = sprintf('/redakce/stahnout/clanek/%d/verze/%d', $article->getId(), $version2->getId());

        $crawler = $this->client->request('GET', $uri1);
        $response1 = $this->client->getResponse();

        $crawler = $this->client->request('GET', $uri2);
        $response2 = $this->client->getResponse();

        $this->assertSame($expectedStatusCode, $response1->getStatusCode());
        $this->assertSame($expectedStatusCode, $response2->getStatusCode());

        if ($expectedStatusCode === 200) {
            $this->assertSame('application/octet-stream', $response1->headers->get('Content-Type'));
            $this->assertSame('application/octet-stream', $response2->headers->get('Content-Type'));
        } else {
            $this->assertSame('text/html; charset=UTF-8', $response1->headers->get('Content-Type'));
            $this->assertSame('text/html; charset=UTF-8', $response2->headers->get('Content-Type'));
        }
    }

    public function provideDownloadArticleVersionAction()
    {
        return [
            ['author', 200],
            ['reviewer', 200],
            ['editor', 200],
            ['chiefeditor', 200],
            ['admin', 403],
            ['reviewer2', 403],
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

        $articleVersion1 = (new ArticleVersion())->setSuffix('sql');
        $articleVersion2 = (new ArticleVersion())->setSuffix('sql');

        $review = new Review();
        $review->setDeadline($tomorrow)->setReviewer($reviewer);

        $article = new Article();
        $article
            ->setOwner($author)
            ->setName('Foo article')
            ->setStatus(ArticleStatus::STATUS_RETURNED)
            ->setEditor($editor)
            ->addReview($review)
            ->addVersion($articleVersion1)
            ->addVersion($articleVersion2)
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

        $articleFolder = $container->getParameter('article_directory');
        $filesystem = $container->get('filesystem');

        $filePath = sprintf('%s/%d-%d', $articleFolder, $article->getId(), $articleVersion1->getId());
        $filesystem->copy(__DIR__ .'/../Fixtures/fixtures.sql', $filePath);

        $filePath = sprintf('%s/%d-%d', $articleFolder, $article->getId(), $articleVersion2->getId());
        $filesystem->copy(__DIR__ .'/../Fixtures/fixtures.sql', $filePath);

        return $article;
    }

    private function prepareFile()
    {
        return new UploadedFile(
            __DIR__ .'/../Fixtures/fixtures.sql',
            'fixtures.sql',
            'application/sql',
            123
        );
    }
}
