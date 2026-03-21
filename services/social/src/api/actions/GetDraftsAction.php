<?php
namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetDraftsAction
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    try {
        $user = (int) $request->getAttribute('id_utilisateur');;

        if (!$user) {
            $response->getBody()->write(json_encode([
                'error' => 'Utilisateur non authentifié'
            ]));
            return $response->withStatus(401)
                            ->withHeader('Content-Type', 'application/json');
        }
        $drafts = $this->postService->getDrafts($user);

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'data' => $drafts
        ]));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Impossible de récupérer les brouillons',
            'details' => $e->getMessage()
        ]));
        return $response->withStatus(500)
                        ->withHeader('Content-Type', 'application/json');
    }
}}
