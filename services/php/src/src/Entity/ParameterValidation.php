<?php

namespace App\Entity;

use App\Repository\ParameterValidationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParameterValidationRepository::class)]
class ParameterValidation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'validators')]
    #[ORM\JoinColumn(name: 'parameter_name', referencedColumnName: 'name', nullable: false)]
    private ?Parameter $parameter = null;

    #[ORM\Column(name: "constraint_class", length: 512)]
    private ?string $constraintClass = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParameter(): ?Parameter
    {
        return $this->parameter;
    }

    public function setParameter(?Parameter $parameter): static
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getConstraintClass(): ?string
    {
        return $this->constraintClass;
    }

    public function setConstraintClass(string $constraintClass): static
    {
        $this->constraintClass = $constraintClass;

        return $this;
    }
}
