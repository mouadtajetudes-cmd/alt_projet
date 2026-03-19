<?php
namespace alt\api\actions;

use alt\core\application\ports\api\CreateLikeDTO;
use alt\core\services\LikeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class CreateLikeAction
{
    private LikeService $likeService;

    public function __construct(LikeService $likeService)
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
            $dto = new CreateLikeDTO($userId,$postId);
            $dto->id_post = $postId;
            $dto->id_utilisateur = $userId;

            $liked = $this->likeService->like($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'liked' => $liked
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($liked ? 201 : 200);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (Throwable $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Erreur interne du serveur'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}