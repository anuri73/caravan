<?php

namespace App\Entity;

class PostParameterValue
{
    private ?string $id = null;

    private Post $post;

    private Parameter $parameter;

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