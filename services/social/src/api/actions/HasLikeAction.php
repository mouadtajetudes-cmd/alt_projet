<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\LikeServiceInterface;

class HasLikeAction
{
    private LikeServiceInterface $likeService;

    public function __construct(LikeServiceInterface $likeService)
    {
        $this->likeService = $likeService;
    }

public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
{
    try {
        $postId = (int) $request->getAttribute('postId');
        $userId = (int) $request->getAttribute('userId');

        $hasLiked = $this->likeService->hasLiked($postId, $userId);

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'liked' => $hasLiked
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

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