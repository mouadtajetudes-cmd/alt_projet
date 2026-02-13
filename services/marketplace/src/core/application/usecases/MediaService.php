<?php

namespace alt\core\application\usecases;

use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;
use alt\core\application\ports\api\MediaServiceInterface;
use alt\core\domain\entities\Media;
use Exception;
use DateTimeImmutable;

class MediaService implements MediaServiceInterface
{
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function getMediaById(int $id): Media
    {
        $media = $this->mediaRepository->findById($id);
        
        if (!$media) {
            throw new Exception("Media not found", 404);
        }

        return $media;
    }

    public function createMedia(string $titre, string $url, string $type = 'image/jpeg'): Media
    {
        $media = new Media(
            0,
            $titre,
            $url,
            $type,
            new DateTimeImmutable()
        );

        return $this->mediaRepository->create($media);
    }

    public function attachMediaToProduct(int $mediaId, int $productId, int $ordre = 0): bool
    {
        $media = $this->mediaRepository->findById($mediaId);
        if (!$media) {
            throw new Exception("Media not found", 404);
        }

        return $this->mediaRepository->attachToProduct($mediaId, $productId, $ordre);
    }

    public function detachMediaFromProduct(int $mediaId, int $productId): bool
    {
        return $this->mediaRepository->detachFromProduct($mediaId, $productId);
    }

    public function getProductMedias(int $productId): array
    {
        return $this->mediaRepository->findByProductId($productId);
    }
}
