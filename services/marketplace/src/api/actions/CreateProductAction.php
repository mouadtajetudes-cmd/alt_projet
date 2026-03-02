<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\CreateProductDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class CreateProductAction
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();

            $dto = new CreateProductDTO(
                $data['nom'],
                (float) $data['prix'],
                (int) $data['id_utilisateur'],
                (int) $data['id_categorie'],
                $data['description'] ?? '',
                $data['statut'] ?? 'disponible',
                isset($data['quantite']) ? (int) $data['quantite'] : 0,
                $data['media_ids'] ?? []
            );

            $product = $this->productService->createProduct($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $product->toArray()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}
