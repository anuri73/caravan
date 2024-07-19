<?php

namespace App\DataTransformer;

use App\Entity\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ParameterSelectTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value): array
    {
        if (null === $value) {
            return [
                'name' => '',
                'category_name' => '',
            ];
        }

        return [
            'parameter' => $value->getName(),
            'category_name' => $value->getCategory()->getName(),
        ];
    }

    public function reverseTransform($value): ?Parameter
    {
        if (!$value['name'] || !$value['category_name']) {
            return null;
        }

        $parameter = $this->entityManager->getRepository(Parameter::class)->findOneBy([
            'name' => $value['name'],
            'category' => $value['category_name'],
        ]);

        if (null === $parameter) {
            throw new TransformationFailedException(sprintf(
                'Parameter with name "%s" and category "%s" does not exist!',
                $value['name'],
                $value['category_name']
            ));
        }

        return $parameter;
    }
}