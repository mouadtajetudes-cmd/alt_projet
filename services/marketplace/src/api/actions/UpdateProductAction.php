<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\UpdateProductDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
            $id = isset($args['id']) ? (int)$args['id'] : 0;

            if ($id <= 0) {
                throw new \InvalidArgumentException('ID produit invalide');
            }

            $data = $request->getParsedBody() ?? [];

            $dto = new UpdateProductDTO(
                $id,
                $data['nom'] ?? null,
                isset($data['prix']) && $data['prix'] !== '' ? (float)$data['prix'] : null,
                isset($data['id_categorie']) && $data['id_categorie'] !== '' ? (int)$data['id_categorie'] : null,
                $data['description'] ?? null,
                $data['statut'] ?? null,
                isset($data['quantite']) && $data['quantite'] !== '' ? (int)$data['quantite'] : null,
                $data['media_ids'] ?? null
            );

            $product = $this->productService->updateProduct($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $product->toArray()
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);

        } catch (\Throwable $e) {
            error_log('UPDATE PRODUCT ERROR: ' . $e->getMessage());

            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Erreur interne du serveur'
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}