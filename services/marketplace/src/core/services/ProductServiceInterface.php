<?php

namespace alt\core\services;

use alt\core\domain\dto\CreateProductDTO;
use alt\core\domain\dto\UpdateProductDTO;
use alt\core\domain\dto\ProductFiltersDTO;
use alt\core\domain\entities\Product;

interface ProductServiceInterface
{
    public function getAllProducts(ProductFiltersDTO $filters): array;

    public function getProductById(int $id): Product;

    public function createProduct(CreateProductDTO $dto): Product;

    public function updateProduct(UpdateProductDTO $dto): Product;

    public function deleteProduct(int $id): bool;
}
