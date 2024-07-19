<?php

namespace App\Entity;

use App\Repository\PostParameterValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: PostParameterValueRepository::class)]
#[ORM\Table(name: 'post_parameter_value')]
#[ORM\UniqueConstraint(name: 'idx_parameter_value', columns: ['post_id', 'parameter_name', 'category_name'])]
class PostParameterValue
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false)]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(name: 'parameter_name', referencedColumnName: 'name', nullable: false)]
    #[ORM\JoinColumn(name: 'category_name', referencedColumnName: 'category_name', nullable: false)]
    private Parameter $parameter;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $value;

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    public function setParameter(Parameter $parameter): static
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}