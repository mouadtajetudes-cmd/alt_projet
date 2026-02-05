<?php
namespace alt\api\actions;

use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\dto\CreateCommentDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class CreateCommentAction
{
    private CommentServiceInterface $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        try {
             $postId = $request->getAttribute('id');
             if ($postId === null) {
            throw new \InvalidArgumentException('postId manquant');
        }

            $data = json_decode((string) $request->getBody(), true);

            if (
                empty($data['details']) ||
                empty($data['id_utilisateur'])
            ) {
                throw new \InvalidArgumentException('Données manquantes');
            }

            $dto = new CreateCommentDTO(
                $data['id_utilisateur'],
                $postId,
                $data['details']
            );

            $comment = $this->commentService->createComment($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => [
                    'details' => $comment->getDetails(),
                    'id_post' => $comment->getIdPost(),
                    'id_utilisateur' => $comment->getIdUtilisateur()
                ]
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur interne du serveur'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
