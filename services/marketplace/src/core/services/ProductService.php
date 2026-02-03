<?php

namespace alt\core\services;

use alt\core\repositories\ProductRepositoryInterface;

class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProductById(string $productId): array
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \Exception("Product not found", 404);
        }

        return $product;
    }
}
