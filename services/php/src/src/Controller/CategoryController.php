<?php

namespace App\Controller;

use App\dataprovider\CategoryDataProvider;
use App\dataprovider\DataProviderInterface;
use App\model\Category;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends CrudController
{
    protected function getDataProvider(): DataProviderInterface
    {
        return new CategoryDataProvider();
    }

    protected function createEntity(Request $request)
    {
        return new Category(1, "Test category");
    }

    protected function updateEntity($entity, Request $request)
    {
        return new Category(1, "Test category");
    }
}
