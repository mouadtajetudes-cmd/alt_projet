<?php

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Media;

interface MediaServiceInterface
{
    public function getMediaById(int $id): Media;

    public function createMedia(string $titre, string $url, string $type = 'image/jpeg'): Media;

    public function attachMediaToProduct(int $mediaId, int $productId, int $ordre = 0): bool;

    public function detachMediaFromProduct(int $mediaId, int $productId): bool;

    public function getProductMedias(int $productId): array;
}
