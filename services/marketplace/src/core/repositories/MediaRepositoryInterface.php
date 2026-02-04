<?php

namespace alt\core\repositories;

use alt\core\domain\entities\Media;

interface MediaRepositoryInterface
{
    public function findById(int $id): ?Media;

    public function create(Media $media): Media;

    public function attachToProduct(int $mediaId, int $productId, int $ordre = 0): bool;

    public function detachFromProduct(int $mediaId, int $productId): bool;

    public function findByProductId(int $productId): array;
}
