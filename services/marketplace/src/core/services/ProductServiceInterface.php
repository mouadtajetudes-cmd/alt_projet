<?php

namespace alt\core\services;

interface ProductServiceInterface
{
    public function getProductById(string $productId): array;
}
