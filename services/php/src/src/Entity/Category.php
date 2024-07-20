<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Category
{
    private ?string $name = null;

    private ?int $priority = null;

    private ?DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $updatedAt;

    private Collection $parents;

    private Collection $children;

    private Collection $parameters;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function initCreatedAt(): static
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function initUpdatedAt(): static
    {
        if ($this->updatedAt === null) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(self $parent): static
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
        }

        return $this;
    }

    public function removeParent(self $parent): static
    {
        $this->parents->removeElement($parent);

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            $child->removeParent($this);
        }

        return $this;
    }

    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): static
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters->add($parameter);
            $parameter->setCategory($this);
        }

        return $this;
    }

    public function removeParameters(Parameter $child): static
    {
        if ($this->parameters->removeElement($child)) {
            $child->setCategory(null);
        }

        return $this;
    }
}
