<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\services\ProductServiceInterface;

class GetProductAction
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $productId = $args['id'] ?? null;

        try {
            $product = $this->productService->getProductById($productId);
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'product' => $product
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 404,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}
