<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute as Serializer;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameter')]
#[ORM\UniqueConstraint(name: 'idx_parameter_name', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
class Parameter
{
    #[ORM\Id]
    #[ORM\Unique]
    #[ORM\Column(name: "name", length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = 0;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'parameters')]
    #[ORM\JoinColumn(name: 'category_name', referencedColumnName: 'name')]
    #[Serializer\MaxDepth(1)]
    private ?Category $category = null;

    /**
     * @var Collection<int, ParameterValidation>
     */
    #[ORM\OneToMany(targetEntity: ParameterValidation::class, mappedBy: 'parameter', orphanRemoval: true)]
    private Collection $validators;

    #[ORM\Column(length: 255, options: ["default" => "textbox"])]
    private ?string $post_form = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $search_form = null;

    #[ORM\Column(nullable: true)]
    private ?int $searchPriority = 0;

    public function __construct()
    {
        $this->validators = new ArrayCollection();
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

    #[ORM\PrePersist]
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

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
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
        return $this->post_form;
    }

    public function setPostForm(string $post_form): static
    {
        $this->post_form = $post_form;

        return $this;
    }

    public function getSearchForm(): ?string
    {
        return $this->search_form;
    }

    public function setSearchForm(string $search_form): static
    {
        $this->search_form = $search_form;

        return $this;
    }

    public function getSearchPriority(): ?int
    {
        return $this->searchPriority;
    }

    public function setSearchPriority(int $searchPriority): static
    {
        $this->searchPriority = $searchPriority;

        return $this;
    }

}