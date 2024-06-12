<?php

namespace App\DataProvider;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Doctrine\Common\Collections\Collection;

class ParameterDataProvider implements DataProviderInterface
{
    private ParameterRepository $parameterRepository;

    public function __construct(ParameterRepository $parameterRepository)
    {
        $this->parameterRepository = $parameterRepository;
    }

    public function find(string $id): ?Parameter
    {
        return $this->parameterRepository->find($id);
    }

    public function next(int $offset, int $limit): Collection
    {
        return $this->parameterRepository->next($offset, $limit);
    }

    public function add($entity): Parameter
    {
        $this->parameterRepository->saveEntity($entity);
        return $entity;
    }

    public function update($entity)
    {
        $this->parameterRepository->saveEntity($entity);
        return $entity;
    }

    public function delete($entity)
    {
        $this->parameterRepository->removeEntity($entity);
        return null;
    }
}