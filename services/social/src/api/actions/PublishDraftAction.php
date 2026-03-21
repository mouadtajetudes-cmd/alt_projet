<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\PostServiceInterface;

class PublishDraftAction
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $postId = (int)$args['id'];
        $currentUserId = $request->getAttribute('id_utilisateur'); 

        try {
            $post = $this->postService->getById($postId);

            if (!$post) {
                throw new \Exception("Brouillon introuvable");
            }

            if ($post->getIdUtilisateur() !== $currentUserId) {
                throw new \Exception("Non autorisé à publier ce brouillon");
            }

           $post= $this->postService->publishDraft($postId);

            $response->getBody()->write(json_encode([
                'success' => "le poste a été publié",
                'post' => $post
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Impossible de publier le brouillon',
                'details' => $e->getMessage()
            ]));

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}