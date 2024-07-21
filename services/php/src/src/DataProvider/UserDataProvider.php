<?php

namespace App\DataProvider;

use App\Entity\User;
use App\Repository\UserRepository;
use http\Exception\InvalidArgumentException;

class UserDataProvider implements DataProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function find(EntityId $id): ?User
    {
        if (!($id instanceof GuidId)) {
            throw new InvalidArgumentException("Unable to find user by given identity");
        }
        return $this->userRepository->find($id->getId());
    }

    public function next(int $offset, int $limit)
    {
        return $this->userRepository->next($offset, $limit);
    }

    public function add($entity)
    {
        $this->userRepository->saveEntity($entity);
        return $entity;
    }

    public function update($entity)
    {
        $this->userRepository->saveEntity($entity);
        return $entity;
    }

    public function delete($entity)
    {
        $this->userRepository->removeEntity($entity);
        return null;
    }
}