<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\CreateAvatarDTO;

class CreateAvatarAction
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
        
        $data = $request->getParsedBody();

        // Validation
        if (empty($data['nom'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar name is required'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        if (empty($data['id_utilisateur'])) {
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
            $dto = new CreateAvatarDTO(
                $data['nom'],
                $data['image'] ?? null,
                (int)$data['id_utilisateur']
            );

            $result = $this->avatarService->createAvatar($dto);
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'avatar' => $result
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
                
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
