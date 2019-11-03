<?php


namespace EditorialBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    protected static function loadFixtures()
    {
        static::bootKernel();

        /** @var ContainerInterface $container */
        $container = static::$kernel->getContainer();

        $tables = [
            'users_roles',
            'users',
            'roles',
        ];

        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $container->get('database_connection');
        $conn->exec('SET foreign_key_checks = 0');
        foreach ($tables as $table) {
            $conn->exec('DELETE FROM '.$table);
        }
        $conn->exec('SET foreign_key_checks = 1');
        $conn->exec(file_get_contents(__DIR__.'/Fixtures/fixtures.sql'));
    }

    protected static function login(Client $client, $username, $password)
    {
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form button')->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        $client->submit($form);
    }
}
