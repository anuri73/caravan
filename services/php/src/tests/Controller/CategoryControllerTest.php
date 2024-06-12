<?php

namespace App\tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    private $client;

    private array $categories = [
        'root1' => [
            "name" => "root1",
            'priority' => 1,
            'parents' => [],
            'children' => [],
        ],
        'root2' => [
            "name" => "root2",
            'priority' => 2
        ],
        'category1' => [
            "name" => 'category1',
            'priority' => 1,
            'parents' => [
                "root1"
            ]
        ],
        'category2' => [
            "name" => 'category2',
            'priority' => 2,
            'parents' => ["root1", "root2"]
        ],
        'category3' => [
            "name" => 'category3',
            'priority' => 3,
            'parents' => []
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

    public function testIndex()
    {
        $category = $this->categories["root1"];

        $this->createCategory($category);

        $category = $this->categories["category1"];

        $this->createCategory($category);

        $category = $this->categories["root2"];

        $this->createCategory($category);

        $category = $this->categories["category2"];

        $this->createCategory($category);

        $category = $this->categories["category3"];

        $this->createCategory($category);

        $this->client->request('GET', '/api/category', ['limit' => 2, 'offset' => 0]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCategory($response[0], $this->categories['category1']);
        $this->assertCategory($response[1], $this->categories['category2']);

        $this->client->request('GET', '/api/category', ['limit' => 3, 'offset' => 2]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCategory($response[0], $this->categories['category3']);
        $this->assertCategory($response[1], $this->categories['root1']);
        $this->assertCategory($response[2], $this->categories['root2']);
    }

    public function testCategoryCreate()
    {
        $category = $this->categories["root1"];

        $this->createCategory($category);

        $this->client->request('GET', "/api/category/{$category['name']}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertCategory(
            json_decode($this->client->getResponse()->getContent(), true),
            $this->categories['root1']
        );
    }

    public function testCategoryCreateWithParent()
    {
        $this->createCategory($this->categories["root1"]);

        $category = $this->categories["category1"];

        $this->createCategory($category);

        $this->client->request('GET', "/api/category/{$category['name']}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertCategory(
            json_decode($this->client->getResponse()->getContent(), true),
            $this->categories['category1']
        );
    }

    public function testCategoryUpdate()
    {
        $this->createCategory($this->categories["root1"]);

        $category = $this->categories["category1"];

        $this->createCategory($category);

        $this->client->jsonRequest('PUT', '/api/category/category1', [
            "name" => 'category1',
            'priority' => 2,
            'parents' => [
                "root1"
            ]
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertCategory(
            json_decode($this->client->getResponse()->getContent(), true),
            [
                "name" => 'category1',
                'priority' => 2,
                'parents' => [
                    "root1"
                ]
            ]
        );
    }

    public function testCategoryDelete()
    {
        $this->createCategory($this->categories["root1"]);

        $category = $this->categories["category1"];

        $this->createCategory($category);

        $this->client->request('DELETE', '/api/category/category1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function assertCategory(array $actual, array $expected)
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertSame($expected['name'], $actual['name'], "Name does not match for category: " . $expected['name']);

        $this->assertArrayHasKey('priority', $actual);
        $this->assertSame($expected['priority'], $actual['priority'], "Priority does not match for category: " . $expected['name']);

        if (isset($expected['parents'])) {
            $this->assertArrayHasKey('parents', $actual);
            foreach ($expected['parents'] as $key => $parent) {
                $this->assertCategory($actual['parents'][$key], $this->categories[$parent]);
            }
        }

        if (isset($expected['children'])) {
            $this->assertArrayHasKey('children', $actual);
            foreach ($expected['children'] as $key => $child) {
                $this->assertCategory($actual['children'][$key], $child);
            }
        }
    }
}