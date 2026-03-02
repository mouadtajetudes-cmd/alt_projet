<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\UpdateAvatarDTO;

class UpdateAvatarAction
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
        $data = $request->getParsedBody();

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

        // Au moins un champ doit être fourni
        if (empty($data['nom']) && empty($data['image'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'At least one field (nom or image) must be provided'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $dto = new UpdateAvatarDTO(
                (int)$avatarId,
                $data['nom'] ?? null,
                $data['image'] ?? null
            );

            $success = $this->avatarService->updateAvatar($dto);
            
            if ($success) {
                $response->getBody()->write(json_encode([
                    'type' => 'resource',
                    'message' => 'Avatar updated successfully'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 404,
                    'message' => 'Avatar not found or update failed'
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
