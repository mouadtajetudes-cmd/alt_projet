<?php

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Product;

interface ProductServiceInterface
{
    public function getAllProducts(ProductFiltersDTO $filters): array;

    public function getProductById(int $id): array;

    public function createProduct(array $data): Product;

    public function updateProduct(int $id, array $data): Product;

    public function deleteProduct(int $id): bool;
}