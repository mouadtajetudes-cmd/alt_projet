<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\AvatarVersionServiceInterface;
use alt\core\application\ports\api\LevelUpDTO;

class LevelUpAvatarAction
{
    private AvatarVersionServiceInterface $avatarVersionService;

    public function __construct(AvatarVersionServiceInterface $avatarVersionService)
    {
        $this->avatarVersionService = $avatarVersionService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $versionId = $args['versionId'] ?? null;
        $data = $request->getParsedBody();

        if (!$versionId) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar version ID is required'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        if (empty($data['new_level'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'New level is required'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $dto = new LevelUpDTO(
                (int)$versionId,
                (int)$data['new_level']
            );

            $success = $this->avatarVersionService->levelUp($dto);
            
            if ($success) {
                $response->getBody()->write(json_encode([
                    'type' => 'resource',
                    'message' => 'Avatar leveled up successfully'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 404,
                    'message' => 'Avatar version not found or level up failed'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
                
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
