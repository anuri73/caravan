<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    private EntityManager $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManager $entityManager)
    {
        parent::__construct($registry, Category::class);
        $this->entityManager = $entityManager;
    }

    public function next(int $offset, int $limit): Collection
    {
        $result = $this->createQueryBuilder("c")
            ->orderBy('c.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($result);
    }

    public function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
