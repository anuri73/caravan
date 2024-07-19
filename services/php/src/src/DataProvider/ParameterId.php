<?php

namespace App\DataProvider;

class ParameterId implements EntityId
{
    private string $name;
    private string $category;

    public function __construct(
        string $name,
        string $category
    )
    {
        $this->name = $name;
        $this->category = $category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}