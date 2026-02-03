<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\services\UserServiceInterface;

class GetUserAction
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $userId = $args['id'] ?? null;

        try {
            $user = $this->userService->getUserById($userId);
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'user' => $user
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
