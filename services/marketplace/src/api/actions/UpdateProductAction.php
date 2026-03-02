<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\UpdateProductDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class UpdateProductAction
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $id = (int) $args['id'];
            $data = $request->getParsedBody();

            $dto = new UpdateProductDTO(
                $id,
                $data['nom'] ?? null,
                isset($data['prix']) ? (float) $data['prix'] : null,
                isset($data['id_categorie']) ? (int) $data['id_categorie'] : null,
                $data['description'] ?? null,
                $data['statut'] ?? null,
                isset($data['quantite']) ? (int) $data['quantite'] : null,
                $data['media_ids'] ?? null
            );

            $product = $this->productService->updateProduct($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $product->toArray()
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}
