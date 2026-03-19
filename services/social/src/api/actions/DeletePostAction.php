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
    $idPost = (int) $args['id'];

    $currentUser = $request->getAttribute('user');
    $currentUserId = $currentUser['id'];

    try {

        $this->postService->deletePost($idPost, $currentUserId);

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Post deleted'
        ]));

        return $response->withHeader('Content-Type','application/json')->withStatus(200);

    } catch (\Exception $e) {

        $response->getBody()->write(json_encode([
            'error' => $e->getMessage()
        ]));

        return $response->withHeader('Content-Type','application/json')->withStatus(403);
    }
}}