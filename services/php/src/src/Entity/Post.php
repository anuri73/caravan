<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::GUID)]
    private ?string $id = null;

    #[ORM\Column(length: 1024)]
    private ?string $title = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Parameter>
     */
    #[ORM\ManyToMany(targetEntity: Parameter::class)]
    #[ORM\JoinTable(name: 'post_parameter_value')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'parameter_id', referencedColumnName: 'name')]
    private Collection $parameterValues;

    public function __construct()
    {
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    /**
     * @return Collection<int, Parameter>
     */
    public function getParameterValues(): Collection
    {
        return $this->parameterValues;
    }

    public function addParameterValue(Parameter $parameterValue): static
    {
        if (!$this->parameterValues->contains($parameterValue)) {
            $this->parameterValues->add($parameterValue);
        }

        return $this;
    }

    public function removeParameterValue(Parameter $parameterValue): static
    {
        $this->parameterValues->removeElement($parameterValue);

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): static
    {
        $this->guid = $guid;

        return $this;
    }
}
