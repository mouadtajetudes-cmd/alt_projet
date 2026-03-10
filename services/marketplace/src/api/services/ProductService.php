<?php

namespace alt\api\actions;

use PDO;
use alt\core\application\usecases\ProductService;
use alt\core\application\ports\api\ProductFiltersDTO;

class GetAllProductsAction
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function execute(int $page = 1, int $limit = 12, array $filters = []): array
    {
        $filtersDTO = new ProductFiltersDTO($page, $limit);
        return $this->productService->getAllProducts($filtersDTO);
    }
}