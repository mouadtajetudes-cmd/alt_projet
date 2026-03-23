<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\ProductFiltersDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetAllProductsAction
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $q = $request->getQueryParams();

            $filters = ProductFiltersDTO::fromArray($q);
            $filters->page = isset($q['page']) ? max(1, (int)$q['page']) : 1;
            $filters->limit = isset($q['limit']) ? max(1, (int)$q['limit']) : 12;

            $result = $this->productService->getAllProducts($filters);

            $rows = $result['products'] ?? (is_array($result) ? $result : []);
            $payload = [
                'status' => 'success',
                'data' => $rows,
                'count' => (int)($result['total'] ?? count($rows)),
                'page' => $filters->page,
                'limit' => $filters->limit
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}