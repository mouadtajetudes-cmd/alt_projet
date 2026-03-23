<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\infra\repositories\PdoUserSelectedAvatarRepository;
use alt\infra\repositories\PdoAvatarVersionRepository;
use alt\infra\repositories\PdoAvatarRepository;

class CreateAvatarAction
{
    private PdoUserSelectedAvatarRepository $userSelectedAvatarRepo;
    private PdoAvatarVersionRepository $avatarVersionRepo;
    private PdoAvatarRepository $avatarRepo;

    public function __construct(
        PdoUserSelectedAvatarRepository $userSelectedAvatarRepo,
        PdoAvatarVersionRepository $avatarVersionRepo,
        PdoAvatarRepository $avatarRepo
    ) {
        $this->userSelectedAvatarRepo = $userSelectedAvatarRepo;
        $this->avatarVersionRepo = $avatarVersionRepo;
        $this->avatarRepo = $avatarRepo;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $uploadedFiles = $request->getUploadedFiles();
        
        $contentType = $request->getHeaderLine('Content-Type');
        
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $data = $_POST;
        } else {
            $data = $request->getParsedBody() ?? [];
        }
        $isAdminCreation = isset($uploadedFiles['image']) && !empty($data['nom']) && !empty($data['versions']);
        
        if ($isAdminCreation) {
            return $this->handleAdminCreation($data, $uploadedFiles, $response);
        } else {
            return $this->handleUserSelection($data, $response);
        }
    }

    private function handleAdminCreation(array $data, array $uploadedFiles, ResponseInterface $response): ResponseInterface
    {
        if (empty($data['nom'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar name is required'
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        if (empty($uploadedFiles['image'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar image file is required'
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        if (empty($data['versions']) || !is_string($data['versions'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => '5 version names are required (one for each level)'
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        $versions = json_decode($data['versions'], true);
        
        if (!is_array($versions) || count($versions) !== 5) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => '5 version names are required (one for each level)'
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        try {
            $imageFile = $uploadedFiles['image'];
            if ($imageFile->getError() !== UPLOAD_ERR_OK) {
                throw new \Exception('Upload failed with error code: ' . $imageFile->getError());
            }
            $clientFilename = $imageFile->getClientFilename();
            $extension = strtolower(pathinfo($clientFilename, PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('Invalid file type. Only PNG, JPG, and JPEG are allowed.');
            }
            $uniqueFilename = uniqid('avatar_', true) . '.' . $extension;
            
            $projectRoot = realpath(__DIR__ . '/../../../../../');
            $uploadDir = $projectRoot . '/frontend/public/avatars';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception('Failed to create upload directory');
                }
            }
            
            $uploadPath = $uploadDir . '/' . $uniqueFilename;
            $imageFile->moveTo($uploadPath);
            $avatarResult = $this->avatarRepo->createSimple(
                $data['nom'],
                $uniqueFilename,
                0
            );
            
            $avatarId = $avatarResult['id_avatar'];

            foreach ($versions as $level => $surnom) {
                $versionData = [
                    'surnom' => $surnom,
                    'level' => $level + 1,
                    'id_avatar' => $avatarId,
                    'id_niveau' => $level + 1
                ];
                
                $this->avatarVersionRepo->create($versionData);
            }

            $response->getBody()->write(json_encode([
                'type' => 'resource',
                'message' => 'Avatar created successfully with 5 versions',
                'avatar' => [
                    'id_avatar' => $avatarId,
                    'nom' => $data['nom'],
                    'image' => $uniqueFilename
                ]
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(201);

        } catch (\Exception $e) {
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 500,
                'message' => 'Failed to create avatar: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500);
        }
    }

    private function handleUserSelection(array $data, ResponseInterface $response): ResponseInterface
    {
        if (empty($data['id_avatar'])) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 400,
                'message' => 'Avatar ID is required (select one of the 5 predefined avatars)'
            ], JSON_UNESCAPED_UNICODE));
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        if (empty($data['id_utilisateur'])) {
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
            $userId = (int)$data['id_utilisateur'];
            $avatarId = (int)$data['id_avatar'];
            
            $hasAvatar = $this->userSelectedAvatarRepo->hasAvatar($userId);
            
            $versions = $this->avatarVersionRepo->findByAvatarId($avatarId);
            
            if (empty($versions)) {
                $response->getBody()->write(json_encode([
                    'type' => 'error',
                    'error' => 404,
                    'message' => 'Avatar not found or invalid'
                ], JSON_UNESCAPED_UNICODE));
                
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(404);
            }
            
            $level1Version = null;
            foreach ($versions as $version) {
                if ($version['level'] == 1) {
                    $level1Version = $version;
                    break;
                }
            }
            
            if (!$level1Version) {
                throw new \Exception('Level 1 version not found for this avatar');
            }
            
            if ($hasAvatar) {
                $success = $this->userSelectedAvatarRepo->changeAvatar(
                    $userId,
                    (int)$level1Version['id_avatar_version']
                );
                
                if ($success) {
                    $result = $this->userSelectedAvatarRepo->findByUserId($userId);
                    
                    $response->getBody()->write(json_encode([
                        'type' => 'resource',
                        'message' => 'Avatar changed successfully',
                        'avatar' => $result
                    ], JSON_UNESCAPED_UNICODE));
                    
                    return $response
                        ->withHeader('Content-Type', 'application/json; charset=utf-8')
                        ->withStatus(200);
                }
            } else {
                $this->userSelectedAvatarRepo->selectAvatar(
                    $userId,
                    (int)$level1Version['id_avatar_version'],
                    0
                );
                
                $result = $this->userSelectedAvatarRepo->findByUserId($userId);
                
                $response->getBody()->write(json_encode([
                    'type' => 'resource',
                    'message' => 'Avatar selected successfully',
                    'avatar' => $result
                ], JSON_UNESCAPED_UNICODE));
                
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(201);
            }
            
            throw new \Exception('Failed to select/change avatar');
                
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
