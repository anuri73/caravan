<?php

namespace App\Tests\TestData;

use Symfony\Component\HttpFoundation\Response;

trait TestParameterData
{
    protected array $parameters = [
        'param1' => [
            'name' => 'param1',
            'category' => 'category1',
            'priority' => 1,
            'postForm' => 'textbox',
            'searchForm' => 'textbox',
            'searchPriority' => 0,
        ],
        'param2' => [
            'name' => 'param2',
            'category' => 'category2',
            'priority' => 1,
            'postForm' => 'textbox',
            'searchForm' => 'textbox',
            'searchPriority' => 0,
        ],
        'param3' => [
            'name' => 'param3',
            'category' => 'category1',
            'priority' => 1,
            'postForm' => 'textbox',
            'searchForm' => 'textbox',
            'searchPriority' => 0,
        ],
        'param4' => [
            'name' => 'param4',
            'category' => 'category2',
            'priority' => 1,
            'postForm' => 'textbox',
            'searchForm' => 'textbox',
            'searchPriority' => 0,
        ]
    ];

    protected function createParameter(array $parameter): void
    {
        $this->client->jsonRequest('POST', '/api/parameter', $parameter);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    protected function createParameters(array $parameters): void
    {
        foreach ($parameters as $parameter) {
            $this->createParameter($parameter);
        }
    }

    protected function assertParameter(array $actual, array $expected): void
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertSame($expected['name'], $actual['name'], "Name does not match for parameter: " . $expected['name']);

        $this->assertArrayHasKey('priority', $actual);
        $this->assertSame($expected['priority'], $actual['priority'], "Priority does not match for parameter: " . $expected['name']);

        if (isset($expected['category']['name'])) {
            $this->assertSame($expected['category']["name"], $actual['category']["name"], "Category does not match for parameter: " . $expected['name']);
        }

        $this->assertArrayHasKey('postForm', $actual);
        $this->assertSame($expected['postForm'], $actual['postForm'], "Post form does not match for parameter: " . $expected['name']);

        $this->assertArrayHasKey('searchForm', $actual);
        $this->assertSame(
            $expected['searchForm'],
            $actual['searchForm'],
            "Search form does not match for parameter: " . $expected['name']
        );

        $this->assertArrayHasKey('searchPriority', $actual);
        $this->assertSame(
            $expected['searchPriority'],
            $actual['searchPriority'],
            "Search priority does not match for parameter: " . $expected['name']
        );
    }
}