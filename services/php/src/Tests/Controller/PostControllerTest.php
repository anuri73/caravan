<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\CaravanTestCase;
use App\Tests\TestData\TestCategoryData;
use App\Tests\TestData\TestParameterData;

class PostControllerTest extends CaravanTestCase
{
    use TestCategoryData;
    use TestParameterData;

    function testCreatePost()
    {
        $user = $this->createUser();

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
        ]);

        $this->client->jsonRequest('POST', '/api/post', [
            'author' => $user->getId(),
            'category' => "category1",
            'parameterValues' => [
                [
                    'parameter' => [
                        'name' => 'param1',
                        'category_name' => 'category1',
                    ],
                    'value' => "test value"
                ],
                [
                    'parameter' => [
                        'name' => 'param2',
                        'category_name' => 'category2',
                    ],
                    'value' => "test value 2"
                ]
            ]
        ]);
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