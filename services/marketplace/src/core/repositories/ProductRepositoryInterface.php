<?php

namespace alt\core\repositories;

interface ProductRepositoryInterface
{
    public function findById(string $productId): ?array;
}
