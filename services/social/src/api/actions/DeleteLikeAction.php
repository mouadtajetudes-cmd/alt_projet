<?php
namespace alt\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use alt\core\application\ports\api\LikeServiceInterface;

class DeleteLikeAction
{
    private LikeServiceInterface $likeService;

    public function __construct(LikeServiceInterface $likeService)
    {
        $this->likeService = $likeService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $postId = (int) $request->getAttribute('id');

            $data = json_decode((string) $request->getBody(), true);
            if (empty($data['id_utilisateur'])) {
                throw new \InvalidArgumentException('Utilisateur manquant');
            }
            $userId = (int) $data['id_utilisateur'];

            $deleted = $this->likeService->unlike($postId, $userId);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'deleted' => $deleted
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur interne du serveur'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}