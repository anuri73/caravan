<?php

namespace App\DataProvider;

interface DataProviderInterface
{
    public function find(string $id);

    public function next(int $offset, int $limit);

    public function add($entity);

    public function update($entity);

    public function delete($entity);
}