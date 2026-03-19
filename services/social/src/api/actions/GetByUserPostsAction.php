<?php
namespace alt\api\actions;

use alt\core\services\PostService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetByUserPostsAction
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idUser = $args['id'];

        $posts = $this->postService->getByUserPosts($idUser);

        $response->getBody()->write(json_encode($posts));

        return $response->withHeader('Content-Type', 'application/json');
    }
}