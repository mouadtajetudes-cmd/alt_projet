<?php

namespace alt\core\application\action;

use alt\core\application\dto\CreatePostDTO;
use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreatePostAction extends JsonError
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(ServerRequestInterface $request,ResponseInterface $response): ResponseInterface {
        try {
           
            $body = $request->getParsedBody();

            if (!isset($body['titre']) || !isset($body['description']) || !isset($body['idUtilisateur'])) {
                throw new \InvalidArgumentException('Paramètres manquants');
            }

            $dto = new CreatePostDTO(
                $body['titre'],
                $body['description'],
                $body['idUtilisateur']
            );

            
            $post = $this->postService->createPost($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $post
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($response, $e->getMessage(), 400);

        } catch (\RuntimeException $e) {
            return $this->jsonError($response, 'Impossible de créer le post', 500);

        } catch (\Exception $e) {
            return $this->jsonError($response, 'Erreur interne du serveur', 500);
        }
    }
}
