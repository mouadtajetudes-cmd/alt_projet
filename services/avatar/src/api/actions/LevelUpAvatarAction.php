<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\infra\repositories\PdoUserSelectedAvatarRepository;
use alt\infra\repositories\PdoAvatarVersionRepository;
use alt\infra\repositories\PdoLevelRepository;

class LevelUpAvatarAction
{
    private PdoUserSelectedAvatarRepository $userSelectedAvatarRepo;
    private PdoAvatarVersionRepository $avatarVersionRepo;
    private PdoLevelRepository $levelRepo;

    public function __construct(
        PdoUserSelectedAvatarRepository $userSelectedAvatarRepo,
        PdoAvatarVersionRepository $avatarVersionRepo,
        PdoLevelRepository $levelRepo
    ) {
        $this->userSelectedAvatarRepo = $userSelectedAvatarRepo;
        $this->avatarVersionRepo = $avatarVersionRepo;
        $this->levelRepo = $levelRepo;
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
            $userAvatar = $this->userSelectedAvatarRepo->findByUserId((int)$userId);
            
            if (!$userAvatar) {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 404,
                    'message' => 'User has no avatar selected'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            $currentLevel = (int)$userAvatar['niveau_actuel'];
            $currentPoints = (int)$userAvatar['current_points'];
            $avatarId = (int)$userAvatar['id_avatar'];
            
            if ($currentLevel >= 5) {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 400,
                    'message' => 'Avatar is already at maximum level (5)'
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
            
            $nextLevel = $currentLevel + 1;
            $levels = $this->levelRepo->findAll();
            $requiredPoints = 0;
            
            foreach ($levels as $level) {
                if ($level['id_niveau'] == $nextLevel) {
                    $requiredPoints = (int)$level['points'];
                    break;
                }
            }
            
            if ($currentPoints < $requiredPoints) {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 400,
                    'message' => "Insufficient points. Have: $currentPoints, Required: $requiredPoints"
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
            
            $versions = $this->avatarVersionRepo->findByAvatarId($avatarId);
            $nextVersionId = null;
            
            foreach ($versions as $version) {
                if ($version['level'] == $nextLevel) {
                    $nextVersionId = (int)$version['id_avatar_version'];
                    break;
                }
            }
            
            if (!$nextVersionId) {
                throw new \Exception("Version for level $nextLevel not found");
            }
            
            $success = $this->userSelectedAvatarRepo->levelUp((int)$userId, $nextVersionId);
            
            if ($success) {
                $updatedAvatar = $this->userSelectedAvatarRepo->findByUserId((int)$userId);
                
                $response->getBody()->write(json_encode([
                    'type' => 'resource',
                    'message' => "Avatar leveled up to level $nextLevel successfully",
                    'avatar' => $updatedAvatar
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                throw new \Exception('Level up failed');
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
