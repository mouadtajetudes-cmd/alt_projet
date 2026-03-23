<?php
namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeletePostAction 
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $idPost = $args['id'];

        try {
            $this->postService->deletePost($idPost);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Post deleted successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'An error occurred while deleting the post'
            ]));

            return $response->withStatus(500)
                            ->withHeader('Content-Type', 'application/json');
        }
    }
}