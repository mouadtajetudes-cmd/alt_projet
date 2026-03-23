<?php

namespace alt\core\application\usecases;

use alt\core\application\ports\spi\repositoryInterfaces\AvatarRepositoryInterface;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\CreateAvatarDTO;
use alt\core\application\ports\api\UpdateAvatarDTO;
use alt\core\domain\entities\Avatar;
use DateTime;

class AvatarService implements AvatarServiceInterface
{
    private AvatarRepositoryInterface $avatarRepository;

    public function __construct(AvatarRepositoryInterface $avatarRepository)
    {
        $this->avatarRepository = $avatarRepository;
    }

    public function getAllAvatars(): array
    {
        return $this->avatarRepository->findAll();
    }

    public function getAvatarById(string $avatarId): ?array
    {
        return $this->avatarRepository->findById((int)$avatarId);
    }

    public function getAvatarsByUserId(string $userId): array
    {
        return $this->avatarRepository->findByUserId($userId) ?? [];
    }

    public function createAvatar(CreateAvatarDTO $dto): array
    {
        $avatar = new Avatar(
            0,
            $dto->nom,
            $dto->image,
            $dto->id_utilisateur,
            new DateTime()
        );
        $result = $this->avatarRepository->create($avatar);
        return $result;
    }

    public function updateAvatar(UpdateAvatarDTO $dto): bool
    {
        $avatar = new Avatar(
            $dto->id_avatar,
            $dto->nom ?? '',
            $dto->image,
            0,
            new DateTime()
        );
        return $this->avatarRepository->update($dto->id_avatar, $avatar);
    }
}
