<?php

namespace alt\core\application\ports\usecases;

use alt\core\repositories\AvatarRepositoryInterface;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\CreateAvatarDTO;
use alt\core\application\ports\api\UpdateAvatarDTO;

class AvatarService implements AvatarServiceInterface
{
    private AvatarRepositoryInterface $avatarRepository;

    public function __construct(AvatarRepositoryInterface $avatarRepository)
    {
        $this->avatarRepository = $avatarRepository;
    }

    public function getAvatarsByUserId(string $userId): array
    {
        return $this->avatarRepository->findByUserId($userId) ?? [];
    }

    public function createAvatar(CreateAvatarDTO $dto): int
    {
        $data = [
            'nom' => $dto->nom,
            'image' => $dto->image,
            'id_utilisateur' => $dto->id_utilisateur
        ];
        return $this->avatarRepository->create($data);
    }

    public function updateAvatar(UpdateAvatarDTO $dto): bool
    {
        $data = [];
        if ($dto->nom !== null) $data['nom'] = $dto->nom;
        if ($dto->image !== null) $data['image'] = $dto->image;
        return $this->avatarRepository->update($dto->id_avatar, $data);
    }
}
