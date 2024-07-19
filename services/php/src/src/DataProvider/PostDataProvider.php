<?php

namespace App\DataProvider;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use http\Exception\InvalidArgumentException;

class PostDataProvider implements DataProviderInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function find(EntityId $id): ?Post
    {
        if (!($id instanceof GuidId)) {
            throw new InvalidArgumentException("Unable to find post by given identity");
        }

        return $this->postRepository->find($id);
    }

    public function next(int $offset, int $limit): Collection
    {
        return $this->postRepository->next($offset, $limit);
    }

    public function add($entity): Post
    {
        if (!($entity instanceof Post)) {
            throw new InvalidArgumentException('Invalid entity provided.');
        }
        foreach ($entity->getParameterValues() as $parameterValue) {
            $parameterValue->setPost($entity);
        }
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