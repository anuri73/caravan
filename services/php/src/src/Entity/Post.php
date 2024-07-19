<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'category_name', referencedColumnName: 'name', nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: PostParameterValue::class, mappedBy: 'id', cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    /**
     * @return Collection<int, PostParameterValue>
     */
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
        if ($this->parameterValues->removeElement($parameterValue)) {
            // set the owning side to null (unless already changed)
            if ($parameterValue->getPost() === $this) {
                $parameterValue->setPost(null);
            }
        }

        return $this;
    }
}
