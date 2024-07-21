<?php

namespace App\Tests\Controller;

use App\DataProvider\GuidId;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Tests\CaravanTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends CaravanTestCase
{
    private UserDataProvider $userDataProvider;
    private UserPasswordHasherInterface $passwordHashService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userDataProvider = self::getContainer()->get(UserDataProvider::class);
        $this->passwordHashService = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testRegister(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => [
                'first' => 'password123',
                'second' => 'password123',
            ],
        ];
        $this->client->request(
            'POST',
            '/api/user/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertEquals('test@example.com', $responseData['email']);

        $user = $this->userDataProvider->find(new GuidId($responseData['id']));
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($this->passwordHashService->isPasswordValid($user, 'password123'));
    }

    public function testRegisterWithInvalidData(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => '',
        ];

        $this->client->request(
            'POST',
            '/api/user/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertNotEmpty($responseData['errors']);
    }
}