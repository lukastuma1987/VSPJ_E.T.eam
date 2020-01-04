<?php

namespace EditorialBundle\Tests\Controller;

use EditorialBundle\Entity\Article;
use EditorialBundle\Entity\ArticleAuthor;
use EditorialBundle\Entity\Magazine;
use EditorialBundle\Entity\User;
use EditorialBundle\Enum\ArticleStatus;
use EditorialBundle\Repository\UserRepository;
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

    /**
     * @dataProvider provideCreateEdit
     */
    public function testCreateArticle($fileName, $mime)
    {
        self::login($this->client, 'author', 'author');

        $crawler = $this->client->request('GET', '/redakce/autor/novy-clanek');
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_article[name]'] = 'Foo';
        $form['editorialbundle_article[magazine]']->select(1);
        $form['editorialbundle_article[file]'] = $this->prepareFile($fileName, $mime);

        $form['editorialbundle_article[authors][0][fullName]'] = 'Foo';
        $form['editorialbundle_article[authors][0][email]'] = 'Foo@Foo.cz';
        $form['editorialbundle_article[authors][0][workplace]'] = 'Foo';
        $form['editorialbundle_article[authors][0][address]'] = 'Foo';

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $this->assertContains('Článek byl úspěšně vytvořen.', $response->getContent());
    }

    /**
     * @dataProvider provideCreateEdit
     */
    public function testEditArticle($fileName, $mime)
    {
        $article = $this->prepareArticle();

        self::login($this->client, 'author', 'author');

        $uri = sprintf('/redakce/autor/clanek-%d/upravit', $article->getId());
        $crawler = $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_article[file]'] = $this->prepareFile($fileName, $mime);

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $this->assertContains('Článek byl úspěšně upraven.', $response->getContent());
    }

    public function provideCreateEdit()
    {
        return [
            ['dummy.doc', 'application/msword'],
            ['dummy.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
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

        $tomorrow = new \DateTime();
        $tomorrow->modify('+1 day');

        $articleAuthor = new ArticleAuthor();
        $articleAuthor->setFullName('Foo')
            ->setEmail('Foo@Foo.cz')
            ->setAddress('Foo')
            ->setWorkplace('Foo')
        ;

        $article = new Article();
        $article
            ->setOwner($author)
            ->setName('Foo article')
            ->setStatus(ArticleStatus::STATUS_RETURNED)
            ->setEditor($editor)
            ->addAuthor($articleAuthor)
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

    private function prepareFile($fileName, $mime)
    {
        return new UploadedFile(__DIR__ .'/../Fixtures/' . $fileName, $fileName, $mime, 123);
    }
}
