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

            $filters = new ProductFiltersDTO();
            $filters->page = isset($q['page']) ? max(1, (int)$q['page']) : 1;
            $filters->limit = isset($q['limit']) ? max(1, (int)$q['limit']) : 12;

            if (property_exists($filters, 'categorie')) {
                $filters->categorie = isset($q['category_id']) && $q['category_id'] !== '' ? (int)$q['category_id'] : null;
            }
            if (property_exists($filters, 'prixMin')) {
                $filters->prixMin = isset($q['min_price']) && $q['min_price'] !== '' ? (float)$q['min_price'] : null;
            }
            if (property_exists($filters, 'prixMax')) {
                $filters->prixMax = isset($q['max_price']) && $q['max_price'] !== '' ? (float)$q['max_price'] : null;
            }
            if (property_exists($filters, 'statut')) {
                $filters->statut = isset($q['status']) && $q['status'] !== '' ? (string)$q['status'] : null;
            }
            if (property_exists($filters, 'search')) {
                $filters->search = $q['search'] ?? null;
            }
            if (property_exists($filters, 'userId')) {
                $filters->userId = isset($q['user_id']) ? (int)$q['user_id'] : null;
            }

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