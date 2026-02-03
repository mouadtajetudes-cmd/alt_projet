<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\services\AvatarServiceInterface;

class GetAvatarAction
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

        try {
            $avatar = $this->avatarService->getAvatarByUserId($userId);
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'avatar' => $avatar
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
