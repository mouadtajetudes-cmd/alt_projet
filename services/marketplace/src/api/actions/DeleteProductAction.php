<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ProductServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteProductAction
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
        try {
            $id = isset($args['id']) ? (int) $args['id'] : 0;

            if ($id <= 0) {
                throw new \InvalidArgumentException('ID produit invalide');
            }

            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                throw new \RuntimeException('Suppression du produit impossible');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès',
                'data' => [
                    'id_produit' => $id
                ]
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

        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (\Throwable $e) {
            error_log('DELETE PRODUCT ERROR: ' . $e->getMessage());

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