<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\AvatarServiceInterface;

class GetUserAvatarAction
{
    private AvatarServiceInterface $avatarService;

    public function __construct(AvatarServiceInterface $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $userId = $args['userId'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'User ID is required'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $avatars = $this->avatarService->getAvatarsByUserId($userId);
            
            $response->getBody()->write(json_encode([
                'type' => 'collection',
                'avatars' => $avatars
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
