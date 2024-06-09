<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    protected function tearDown(): void
    {
        // Remove exception handler
        restore_exception_handler();
        parent::tearDown();
    }

    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/api/category');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testShow()
    {
        $client = static::createClient();
        $client->request('GET', '/api/category/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreate()
    {
        $client = static::createClient();
        $client->request('POST', '/api/category', [], [], [], json_encode(['label' => 'New Category']));

        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdate()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/category/1', [], [], [], json_encode(['label' => 'Updated Category']));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/category/1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}