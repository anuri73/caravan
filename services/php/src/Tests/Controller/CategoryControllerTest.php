<?php

namespace App\Tests\Controller;

use App\Tests\CaravanTestCase;
use App\Tests\TestData\TestCategoryData;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends CaravanTestCase
{
    use TestCategoryData;

    public function testIndex()
    {
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["category1"],
            $this->categories["root2"],
            $this->categories["category2"],
            $this->categories["category3"],
        ]);

        $this->client->request('GET', '/api/category', ['limit' => 2, 'offset' => 0]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCategoryShort($response[0], $this->categories['category1']);
        $this->assertCategoryShort($response[1], $this->categories['category2']);

        $this->client->request('GET', '/api/category', ['limit' => 3, 'offset' => 2]);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCategoryShort($response[0], $this->categories['category3']);
        $this->assertCategoryShort($response[1], $this->categories['root1']);
        $this->assertCategoryShort($response[2], $this->categories['root2']);
    }

    public function testCategoryCreate()
    {
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["category1"],
        ]);

        $this->client->request('GET', "/api/category/{$this->categories["category1"]['name']}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertCategory(
            json_decode($this->client->getResponse()->getContent(), true),
            $this->categories['category1']
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
}