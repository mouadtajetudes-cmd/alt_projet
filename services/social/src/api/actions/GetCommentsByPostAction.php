<?php
namespace alt\api\actions;

use alt\core\application\ports\api\CommentServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class GetCommentsByPostAction
{
    private CommentServiceInterface $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $postId = $request->getAttribute('id');

            if ($postId === null) {
                throw new \InvalidArgumentException('postId manquant');
            }

            $comments = $this->commentService->getCommentsByPost( $postId);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $comments
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (Throwable $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur interne du serveur']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
