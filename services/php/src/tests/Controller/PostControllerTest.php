<?php

namespace App\tests\Controller;

use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    private $client;

    private array $categories = [
        'cat1' => [
            "name" => "cat1",
            'priority' => 1,
        ],
        'cat2' => [
            "name" => "cat2",
            'priority' => 2
        ]
    ];

    private array $parameters = [
        'param1' => [
            'name' => 'param1',
            'category' => 'cat1',
            'priority' => 1,
            'postForm' => 'textbox'
        ],
        'param2' => [
            'name' => 'param2',
            'category' => 'cat2',
            'priority' => 1,
            'postForm' => 'textbox'
        ],
        'param3' => [
            'name' => 'param3',
            'category' => 'cat1',
            'priority' => 1,
            'postForm' => 'textbox'
        ],
        'param4' => [
            'name' => 'param4',
            'category' => 'cat2',
            'priority' => 1,
            'postForm' => 'textbox'
        ]
    ];

    /** @var EntityManagerInterface */
    protected $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->purgeDatabase();

        parent::setUp();
    }

    private function purgeDatabase()
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
        $this->entityManager = null;
    }

    function testCreatePost()
    {
        $user = $this->createUser();

        $cat1 = $this->categories["cat1"];

        $this->createCategory($cat1);

        $cat2 = $this->categories["cat2"];

        $this->createCategory($cat2);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $parameter = $this->parameters['param2'];

        $this->createParameter($parameter);

        $this->client->jsonRequest('POST', '/api/post', [
            'author' => $user->getId(),
            'category' => "cat1",
            'parameterValues' => [
                [
                    'parameter' => [
                        'name' => 'param1',
                        'category_name' => 'cat1',
                    ],
                    'value' => "test value"
                ],
                [
                    'parameter' => [
                        'name' => 'param2',
                        'category_name' => 'cat2',
                    ],
                    'value' => "test value 2"
                ]
            ]
        ]);
    }

    private function createCategory(array $categoryData)
    {
        $this->client->jsonRequest('POST', '/api/category', $categoryData);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    private function createParameter(array $parameterData)
    {
        $this->client->jsonRequest('POST', '/api/parameter', $parameterData);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail("test@company.com");
        $user->setPasswordHash('$2y$13$fSyj4mw7Qr1V2RpC0pwVO.dn3ng0zl2Xif6UmqJzPym9bPxAI.1Jm');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@company.com']);
    }
}