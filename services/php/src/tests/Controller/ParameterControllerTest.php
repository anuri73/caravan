<?php

namespace App\tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ParameterControllerTest extends WebTestCase
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
            'priority' => 1,
        ],
        'param2' => [
            'name' => 'param2',
            'priority' => 1,
        ],
        'param3' => [
            'name' => 'param3',
            'priority' => 1,
        ],
        'param4' => [
            'name' => 'param4',
            'priority' => 1,
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

    public function testIndex()
    {
        $category = $this->categories["cat1"];

        $this->createCategory($category);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $parameter = $this->parameters['param2'];

        $this->createParameter($parameter);

        $parameter = $this->parameters['param3'];

        $this->createParameter($parameter);

        $parameter = $this->parameters['param4'];

        $this->createParameter($parameter);

        $this->client->request('GET', '/api/parameter', ['limit' => 2, 'offset' => 0]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertParameter($response[0], $this->parameters['param1']);
        $this->assertParameter($response[1], $this->parameters['param2']);

        $this->client->request('GET', '/api/parameter', ['limit' => 3, 'offset' => 2]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertParameter($response[0], $this->parameters['param3']);
        $this->assertParameter($response[1], $this->parameters['param4']);
    }

    public function testParameterCreate()
    {
        $category = $this->categories["cat1"];

        $this->createCategory($category);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $this->client->request('GET', "/api/parameter/{$parameter['name']}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertParameter(
            json_decode($this->client->getResponse()->getContent(), true),
            $this->parameters['param1']
        );
    }

    public function testParameterUpdate()
    {
        $category = $this->categories["cat1"];

        $this->createCategory($category);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $this->client->jsonRequest('PUT', '/api/parameter/param1', [
            "name" => 'param1',
            'priority' => 2,
            'category' => "cat1"
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertParameter(
            json_decode($this->client->getResponse()->getContent(), true),
            [
                "name" => 'param1',
                'priority' => 2,
                'category' => ["name" => "cat1"]
            ]
        );
    }

    public function testCategoryDelete()
    {
        $this->createCategory($this->categories["cat1"]);

        $this->createParameter($this->parameters["param1"]);

        $this->client->request('DELETE', '/api/parameter/param1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function assertParameter(array $actual, array $expected)
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertSame($expected['name'], $actual['name'], "Name does not match for category: " . $expected['name']);

        $this->assertArrayHasKey('priority', $actual);
        $this->assertSame($expected['priority'], $actual['priority'], "Priority does not match for category: " . $expected['name']);

        if (isset($expected['category']['name'])) {
            $this->assertSame($expected['category']["name"], $actual['category']["name"], "Category does not match for parameter: " . $expected['name']);
        }
    }
}