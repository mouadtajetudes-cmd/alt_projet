<?php

namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\ReactionServiceInterface;

class GetReactionsByPostAction
{
    private ReactionServiceInterface $reactionService;
    private PostServiceInterface $postService;

    public function __construct(ReactionServiceInterface $reactionService,PostServiceInterface $postService)
    {
        $this->reactionService = $reactionService;
        $this->postService=$postService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args
    ): ResponseInterface {
        $postId = $args['id'] ?? null;

        if ($postId === null) {
            $response->getBody()->write(json_encode([
                'error' => 'postId manquant'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $post = $this->postService->getById($postId);

        $reactions = $this->reactionService->getReactionsByPost($post);
        $reactionsArray = array_map(fn($reaction) => [
    'id' => $reaction->getIdReaction(),
    'type' => $reaction->getType(),
    'id_utilisateur' => $reaction->getIdUtilisateur(),
    'post' => $reaction->getPost()->getIdPost()
], $reactions);


 $response->getBody()->write(json_encode([
        'status' => 'success',
        'data' => $reactionsArray
    ]));        return $response->withHeader('Content-Type', 'application/json');
    }
}
