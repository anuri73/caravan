<?php

namespace App\Controller;

use App\dataprovider\CategoryDataProvider;
use App\dataprovider\DataProviderInterface;
use App\Entity\Category;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends CrudController
{
    private CategoryDataProvider $categoryDataProvider;

    public function __construct(CategoryDataProvider $categoryDataProvider)
    {
        $this->categoryDataProvider = $categoryDataProvider;
    }

    protected function getDataProvider(): DataProviderInterface
    {
        return $this->categoryDataProvider;
    }

    protected function createEntity(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return (new Category())
            ->setName($data['name'] ?? null)
            ->setPriority($data['priority'] ?? null);
    }

    protected function updateEntity($entity, Request $request)
    {
        if (!($entity instanceof Category)) {
            throw new InvalidArgumentException('Wrong entity provided');
        }

        $data = json_decode($request->getContent(), true);

        return $entity
            ->setName($data['name'] ?? null)
            ->setPriority($data['priority'] ?? null);
    }
}
