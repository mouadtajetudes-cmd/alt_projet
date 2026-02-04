<?php

namespace alt\core\repositories;

use alt\core\domain\entities\Product;

interface ProductRepositoryInterface
{
    public function findAll(array $filters = []): array;

    public function findById(int $id): ?Product;

    public function create(Product $product): Product;

    public function update(Product $product): Product;

    public function delete(int $id): bool;
}
