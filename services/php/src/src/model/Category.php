<?php

namespace App\model;
class Category
{
    public string $id;
    public string $label;
    public null|Category $parent;

    public function __construct(string $id, string $label, null|Category $parent = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->parent = $parent;
    }
}