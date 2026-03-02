<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\AdServiceInterface;
use alt\core\application\ports\api\CreateAdDTO;
use alt\core\application\ports\api\UpdateAdDTO;
use alt\core\application\ports\spi\repositoryInterfaces\AdRepositoryInterface;
use alt\core\domain\entities\Ad;

class AdService implements AdServiceInterface
{
    private AdRepositoryInterface $adRepository;

    public function __construct(
        AdRepositoryInterface $adRepository
    ) {
        $this->adRepository = $adRepository;
    }

    public function getAllAds(): array
    {
        return $this->adRepository->findAll();
    }

    public function getAdById(int $id): Ad
    {
        return $this->adRepository->findById($id);
    }

    public function createAd(CreateAdDTO $dto): Ad
    {
        $ad = new Ad(
            null,
            $dto->titre,
            $dto->description,
            $dto->image,
            $dto->lien,
            $dto->actif ?? false,
            null
        );

        
        return $this->adRepository->create($ad);
    }

    public function updateAd(int $id, UpdateAdDTO $dto): Ad
    {
        $data = [
            'titre' => $dto->titre,
            'description' => $dto->description,
            'image' => $dto->image,
            'lien' => $dto->lien,
            'actif' => $dto->actif ? 'true' : 'false'
        ];
        
        return $this->adRepository->update($id, $data);
    }

    public function deleteAd(int $id): bool
    {
        return $this->adRepository->delete($id);
    }
}
