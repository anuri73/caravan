<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Parameter
{
    private ?string $name = null;

    private ?int $priority = 0;

    private ?DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $updatedAt;

    private ?Category $category = null;

    private Collection $validators;

    private ?string $postForm = null;

    private ?string $searchForm = null;

    private ?int $searchPriority = 0;

    private ?self $parent = null;

    private Collection $children;

    public function __construct()
    {
        $this->validators = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return Collection<int, ParameterValidation>
     */
    public function getValidators(): Collection
    {
        return $this->validators;
    }

    public function addValidator(ParameterValidation $validator): static
    {
        if (!$this->validators->contains($validator)) {
            $this->validators->add($validator);
            $validator->setParameter($this);
        }

        return $this;
    }

    public function removeValidator(ParameterValidation $validator): static
    {
        if ($this->validators->removeElement($validator)) {
            // set the owning side to null (unless already changed)
            if ($validator->getParameter() === $this) {
                $validator->setParameter(null);
            }
        }

        return $this;
    }

    public function getPostForm(): ?string
    {
        return $this->postForm;
    }

    public function setPostForm(?string $postForm = null): static
    {
        $this->postForm = $postForm;

        return $this;
    }

    public function getSearchForm(): ?string
    {
        return $this->searchForm;
    }

    public function setSearchForm(?string $searchForm = null): static
    {
        $this->searchForm = $searchForm;

        return $this;
    }

    public function getSearchPriority(): ?int
    {
        return $this->searchPriority;
    }

    public function setSearchPriority(?int $searchPriority = null): static
    {
        $this->searchPriority = $searchPriority;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

}