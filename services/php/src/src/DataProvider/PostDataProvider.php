<?php

namespace App\DataProvider;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;

class PostDataProvider implements DataProviderInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function find(string $id): ?Post
    {
        return $this->postRepository->find($id);
    }

    public function next(int $offset, int $limit): Collection
    {
        return $this->postRepository->next($offset, $limit);
    }

    public function add($entity): Post
    {
        $this->postRepository->saveEntity($entity);
        return $entity;
    }

    public function update($entity)
    {
        $this->postRepository->saveEntity($entity);
        return $entity;
    }

    public function delete($entity)
    {
        $this->postRepository->removeEntity($entity);
        return null;
    }
}