<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\services\PostServiceInterface;

class GetPostAction
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $postId = $args['id'] ?? null;

        try {
            $post = $this->postService->getPostById($postId);
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'post' => $post
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 404,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}
