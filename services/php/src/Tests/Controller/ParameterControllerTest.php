<?php

namespace App\Tests\Controller;

use App\Tests\CaravanTestCase;
use App\Tests\TestData\TestCategoryData;
use App\Tests\TestData\TestParameterData;
use Symfony\Component\HttpFoundation\Response;

class ParameterControllerTest extends CaravanTestCase
{
    use TestCategoryData;
    use TestParameterData;

    public function testIndex()
    {
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["root2"],
            $this->categories["category1"],
            $this->categories["category2"],
            $this->categories["category3"]
        ]);

        $this->createParameters([
            $this->parameters['param1'],
            $this->parameters['param2'],
            $this->parameters['param3'],
            $this->parameters['param4'],
        ]);

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
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["root2"],
            $this->categories["category1"],
            $this->categories["category2"],
            $this->categories["category3"]
        ]);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $this->client->request('GET', "/api/parameter/{$parameter['category']}/{$parameter['name']}");
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertParameter(
            json_decode($this->client->getResponse()->getContent(), true),
            $this->parameters['param1']
        );
    }

    public function testParameterUpdate()
    {
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["category1"],
            $this->categories["root2"],
            $this->categories["category2"],
            $this->categories["category3"],
        ]);

        $parameter = $this->parameters['param1'];

        $this->createParameter($parameter);

        $this->client->jsonRequest('PUT', "/api/parameter/{$this->categories["category1"]["name"]}/{$this->parameters["param1"]["name"]}", [
            "name" => 'param1',
            'priority' => 2,
            'category' => "category1",
            'postForm' => 'textbox',
            'searchForm' => 'textbox',
            'searchPriority' => 0,
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertParameter(
            json_decode($this->client->getResponse()->getContent(), true),
            [
                "name" => 'param1',
                'priority' => 2,
                'category' => ["name" => "category1"],
                'postForm' => 'textbox',
                'searchForm' => 'textbox',
                'searchPriority' => 0,
            ]
        );
    }

    public function testCategoryDelete()
    {
        $this->createCategories([
            $this->categories["root1"],
            $this->categories["category1"],
            $this->categories["root2"],
            $this->categories["category2"],
            $this->categories["category3"],
        ]);

        $this->createParameter($this->parameters["param1"]);

        $this->client->request('DELETE', "/api/parameter/{$this->categories["category1"]["name"]}/{$this->parameters["param1"]["name"]}");

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
}