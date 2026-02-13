<?php

namespace alt\api\actions;

use alt\core\application\dto\UpdatePostDTO;
use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdatePostAction 
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idPost = $args['id'];
        $data = $request->getParsedBody();

        if (empty($data['content'])) {
            $response->getBody()->write(json_encode(['error' => 'Content is required']));
            return $response->withStatus(400)
                            ->withHeader('Content-Type', 'application/json');
        }

        try {

            $updatedPost = $this->postService->updatePost($idPost, $data);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $updatedPost
            ]));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'An error occurred while updating the post'
            ]));

            return $response->withStatus(500)
                            ->withHeader('Content-Type', 'application/json');
        }
    }
}
