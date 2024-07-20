<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CaravanTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManagerInterface|ObjectManager $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->purgeDatabase();

        parent::setUp();
    }

    private function purgeDatabase(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    protected function tearDown(): void
    {
        // Remove exception handler
        restore_exception_handler();
        parent::tearDown();

        $this->entityManager->close();
    }
}