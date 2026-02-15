<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UploadAvatarAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Get user ID from JWT token stored in request attribute (set by AuthMiddleware)
        $userId = $request->getAttribute('user_id');
        
        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'Non authentifié']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['avatar'])) {
            $response->getBody()->write(json_encode(['error' => 'Aucun fichier envoyé']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $avatarFile = $uploadedFiles['avatar'];
        
        if ($avatarFile->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors du téléchargement']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $avatarFile->getClientMediaType();
        
        if (!in_array($fileType, $allowedTypes)) {
            $response->getBody()->write(json_encode(['error' => 'Type de fichier non autorisé']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        // Validate file size (5 MB max)
        if ($avatarFile->getSize() > 5 * 1024 * 1024) {
            $response->getBody()->write(json_encode(['error' => 'Fichier trop volumineux (max 5 Mo)']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        // Generate unique filename
        $extension = pathinfo($avatarFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../../public/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filepath = $uploadDir . '/' . $filename;
        
        try {
            // Move uploaded file
            $avatarFile->moveTo($filepath);
            
            // Update user's avatar_url in database
            $avatarUrl = '/uploads/avatars/' . $filename;
            
            // Update user in database - we need to add this method to UserService
            $this->userService->updateUserAvatar((int)$userId, $avatarUrl);
            
            $response->getBody()->write(json_encode([
                'message' => 'Avatar mis à jour avec succès',
                'avatar_url' => $avatarUrl
            ]));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
