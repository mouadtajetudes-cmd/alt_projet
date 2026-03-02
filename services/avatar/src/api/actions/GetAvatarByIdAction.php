<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\AvatarServiceInterface;

class GetAvatarByIdAction
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
        
        $avatarId = $args['avatarId'] ?? null;

        if (!$avatarId) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar ID is required'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $avatar = $this->avatarService->getAvatarById($avatarId);
            
            if (!$avatar) {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 404,
                    'message' => 'Avatar not found'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            $response->getBody()->write(json_encode($avatar));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 500,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
