<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Ad;

interface AdServiceInterface
{
    public function getAllAds(): array;
    public function getAdById(int $id): Ad;
    public function createAd(CreateAdDTO $dto): Ad;
    public function updateAd(int $id, UpdateAdDTO $dto): Ad;
    public function deleteAd(int $id): bool;
}
