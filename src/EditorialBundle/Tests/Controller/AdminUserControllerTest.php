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

class AdminUserControllerTest extends WebTestCase
{
    /** @var Client $client  */
    private $client;

    protected function setUp()
    {
        self::loadFixtures();
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->client = null;
        parent::tearDown();
    }

    /**
     * @dataProvider provideEditAction
     */
    public function testEditAction(array $roles, $expectedRoles, $changePassword)
    {

        self::login($this->client, 'admin', 'admin');

        $crawler = $this->client->request('GET', '/redakce/admin/uzivatel/novy');
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_user[username]'] = 'Foo User';
        $form['editorialbundle_user[email]'] = 'foo@foo.cz';
        $form['editorialbundle_user[plaintextPassword][first]'] = '12345678';
        $form['editorialbundle_user[plaintextPassword][second]'] = '12345678';


        foreach ($roles as $role) {
            $form[sprintf('editorialbundle_user[roles][%d]', $role - 1)]->tick();
        }

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo User', $responseContent);
        $this->assertContains($expectedRoles, $responseContent);

        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        /** @var UserRepository $repository */
        $repository = $em->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneByEmail('foo@foo.cz');
        $uri = sprintf('/redakce/admin/uzivatel/%d/upravit', $user->getId());

        $crawler = $this->client->request('GET', $uri);
        $response = $this->client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $form = $crawler->filter('form button')->form();
        $form['editorialbundle_user[username]'] = 'Foo User 2';
        $form['editorialbundle_user[email]'] = 'foo2@foo.cz';
        $form['editorialbundle_user[roles]'] = [1, 2, 3, 4, 5];

        if ($changePassword) {
            $form['editorialbundle_user[plaintextPassword][first]'] = '123456789';
            $form['editorialbundle_user[plaintextPassword][second]'] = '123456789';
        }

        $crawler = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertSame(302, $response->getStatusCode());

        $crawler = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertContains('Foo User 2', $response->getContent());
        $this->assertContains('Autor, Recenzent, Redaktor, Šéfredaktor, Administrátor', $response->getContent());

        self::login($this->client, 'Foo User 2', $changePassword ? '123456789' : '12345678');
    }

    public function provideEditAction()
    {
        return [
            [[1], 'Autor', true],
            [[2], 'Recenzent', false],
            [[3], 'Redaktor', true],
            [[4], 'Šéfredaktor', false],
            [[5], 'Administrátor', true],
            [[1, 2, 4], 'Autor, Recenzent, Šéfredaktor', false],
            [[3, 5], 'Redaktor, Administrátor', true],
        ];
    }
}
