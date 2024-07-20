<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Post
{
    private ?string $id = null;

    private ?DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $updatedAt;

    private ?User $author = null;

    private ?Category $category = null;

    private Collection $parameterValues;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->parameterValues = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getParameterValues(): Collection
    {
        return $this->parameterValues;
    }

    public function addParameterValue(PostParameterValue $parameterValue): static
    {
        if (!$this->parameterValues->contains($parameterValue)) {
            $this->parameterValues->add($parameterValue);
            $parameterValue->setPost($this);
        }

        return $this;
    }

    public function removeParameterValue(PostParameterValue $parameterValue): static
    {
        $this->parameterValues->removeElement($parameterValue);

        return $this;
    }
}
