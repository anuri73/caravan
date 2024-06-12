<?php

namespace App\DataProvider;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;

class CategoryDataProvider implements DataProviderInterface
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function find(string $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function next(int $offset, int $limit): Collection
    {
        return $this->categoryRepository->next($offset, $limit);
    }

    public function add($entity): Category
    {
        $this->categoryRepository->saveEntity($entity);
        return $entity;
    }

    public function update($entity)
    {
        $this->categoryRepository->saveEntity($entity);
        return $entity;
    }

    public function delete($entity)
    {
        $this->categoryRepository->removeEntity($entity);
        return null;
    }
}