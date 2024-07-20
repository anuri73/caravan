<?php

namespace App\Entity;

class ParameterValidation
{
    private ?int $id = null;

    private ?Parameter $parameter = null;

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
