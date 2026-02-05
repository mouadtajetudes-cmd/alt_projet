<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\ProductFiltersDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllProductsAction
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();

        $filters = new ProductFiltersDTO(
            isset($params['categorie']) ? (int) $params['categorie'] : null,
            isset($params['prix_min']) ? (float) $params['prix_min'] : null,
            isset($params['prix_max']) ? (float) $params['prix_max'] : null,
            $params['statut'] ?? null,
            $params['search'] ?? null,
            isset($params['user_id']) ? (int) $params['user_id'] : null,
            isset($params['limit']) ? (int) $params['limit'] : 20,
            isset($params['page']) ? (int) $params['page'] : 1
        );

        $products = $this->productService->getAllProducts($filters);
        $data = array_map(fn($p) => $p->toArray(), $products);

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'data' => $data,
            'count' => count($data)
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
