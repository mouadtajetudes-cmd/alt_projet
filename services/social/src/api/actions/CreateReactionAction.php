<?php

namespace alt\api\actions;

use alt\core\application\dto\CreateReactionDTO;
use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\ReactionServiceInterface;
use Throwable;
use alt\core\domain\entities\Reaction;

class CreateReactionAction
{
    private ReactionServiceInterface $reactionService;
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService,ReactionServiceInterface $reactionService)
    {
        $this->postService = $postService;
        $this->reactionService=$reactionService;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        try {
            $postId = $args['id'] ?? null;
            
            if ($postId === null) {
                throw new \InvalidArgumentException('postId manquant');
            }

            $data = $request->getParsedBody();
            if (!isset($data['type']) || !isset($data['idutilisateur'])) {
                throw new \InvalidArgumentException('type ou idutilisateur manquant');
            }

            $post = $this->postService->getById($postId);
            
            

           $reactiondto = new CreateReactionDTO(
                $post,
                $data['idutilisateur'],
                $data['type']
            );
            
            $reaction = $this->reactionService->createReaction($reactiondto);

            $reactionArray = [
    'post' => [
        'id' => $reaction->getPost()->getIdPost(),
        'titre' => $reaction->getPost()->getTitre(),
        'description' => $reaction->getPost()->getDescription(),
        'date_publication' => $reaction->getPost()->getDatePublication(),
        'id_utilisateur' => $reaction->getPost()->getIdUtilisateur(),
    ],
    'id_utilisateur' => $reaction->getIdUtilisateur(),
    'type' => $reaction->getType(),
];
$response->getBody()->write(json_encode([
    'status' => 'success',
    'data' => $reactionArray
]));

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');

        } catch (\InvalidArgumentException $e) {

            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');

        } catch (Throwable $e) {


            $response->getBody()->write(json_encode([
                'error' => 'Erreur interne du serveur'
            ]));

            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
