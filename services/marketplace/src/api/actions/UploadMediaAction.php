<?php

namespace alt\api\actions;

use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;
use alt\core\domain\entities\Media;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;
use DateTimeImmutable;

class UploadMediaAction
{
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $uploadedFiles = $request->getUploadedFiles();
            
            if (empty($uploadedFiles['file'])) {
                throw new Exception('Aucun fichier uploadé', 400);
            }

            $uploadedFile = $uploadedFiles['file'];
            
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                throw new Exception('Erreur lors de l\'upload du fichier', 400);
            }

            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $uploadedFile->getClientMediaType();
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Type de fichier non autorisé. Formats acceptés: JPG, PNG, GIF, WebP', 400);
            }

            // Vérifier la taille (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($uploadedFile->getSize() > $maxSize) {
                throw new Exception('Le fichier est trop volumineux. Taille maximale: 5MB', 400);
            }

            // Générer un nom unique pour le fichier
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $filename = uniqid('product_', true) . '.' . $extension;
            
            // Créer le dossier uploads s'il n'existe pas
            $uploadPath = __DIR__ . '/../../../../../../../gateway/images/products/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Déplacer le fichier
            $uploadedFile->moveTo($uploadPath . $filename);

            // Créer l'entrée dans la base de données
            $media = new Media(
                0, // ID sera généré par la base
                $uploadedFile->getClientFilename(),
                '/images/products/' . $filename,
                $fileType,
                new DateTimeImmutable()
            );

            $savedMedia = $this->mediaRepository->create($media);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $savedMedia->toArray()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 100 && $e->getCode() < 600 ? $e->getCode() : 500;
            
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode);
        }
    }
}
