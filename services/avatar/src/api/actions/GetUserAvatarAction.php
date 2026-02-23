<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\infra\repositories\PdoUserSelectedAvatarRepository;

class GetUserAvatarAction
{
    private PdoUserSelectedAvatarRepository $userSelectedAvatarRepo;

    public function __construct(PdoUserSelectedAvatarRepository $userSelectedAvatarRepo)
    {
        $this->userSelectedAvatarRepo = $userSelectedAvatarRepo;
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
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        try {
            $avatar = $this->userSelectedAvatarRepo->findByUserId((int)$userId);
            
            if (!$avatar) {
                $response->getBody()->write(json_encode([
                    'type' => 'resource',
                    'message' => 'User has no avatar selected',
                    'avatar' => null
                ], JSON_UNESCAPED_UNICODE));
                
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(200);
            }
            
            $mappedAvatar = [
                'id_avatar' => $avatar['id_avatar'],
                'nom' => $avatar['avatar_nom'],
                'image' => $avatar['avatar_image'],
                'niveau_actuel' => $avatar['niveau_actuel'],
                'nom_niveau' => $avatar['niveau_nom'],
                'current_points' => $avatar['current_points'],
                'points_requis_niveau' => $avatar['points_requis_niveau'],
                'created_at' => $avatar['selected_at'],
                'updated_at' => $avatar['updated_at']
            ];
            
            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'avatar' => $mappedAvatar
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 500,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500);
        }
    }
}
