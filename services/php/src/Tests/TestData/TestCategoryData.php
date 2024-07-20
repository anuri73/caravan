<?php

namespace App\Tests\TestData;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

trait TestCategoryData
{
    protected array $categories = [
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

    protected KernelBrowser $client;

    protected function createCategory(array $data): void
    {
        $this->client->jsonRequest('POST', '/api/category', $data);

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    protected function createCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $this->createCategory($category);
        }
    }

    protected function assertCategoryShort(array $actual, array $expected): void
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertSame($expected['name'], $actual['name'], "Name does not match for category: " . $expected['name']);

        $this->assertArrayHasKey('priority', $actual);
        $this->assertSame($expected['priority'], $actual['priority'], "Priority does not match for category: " . $expected['name']);
    }

    protected function assertCategory(array $actual, array $expected): void
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertSame($expected['name'], $actual['name'], "Name does not match for category: " . $expected['name']);

        $this->assertArrayHasKey('priority', $actual);
        $this->assertSame($expected['priority'], $actual['priority'], "Priority does not match for category: " . $expected['name']);

        if (isset($expected['children'])) {
            $this->assertArrayHasKey('children', $actual);
            foreach ($expected['children'] as $key => $child) {
                $this->assertCategory($actual['children'][$key], $child);
            }
        }
    }
}