<?php

namespace App\dataprovider;

use App\model\Category;

class CategoryDataProvider implements DataProviderInterface
{
    public function find(string $id)
    {
        return new Category(1, "Test category");
    }

    public function next(int $offset, int $limit)
    {
        return [new Category(1, "Test category")];
    }

    public function add($entity)
    {
        return new Category(1, "Test category");
    }

    public function update($entity)
    {
        return new Category(1, "Test category");
    }

    public function delete($entity)
    {
        return null;
    }
}