<?php

namespace alt\core\application\ports\usecases;

use alt\core\repositories\AvatarVersionRepositoryInterface;
use alt\core\application\ports\api\AvatarVersionServiceInterface;
use alt\core\application\ports\api\LevelUpDTO;

class AvatarVersionService implements AvatarVersionServiceInterface
{
    private AvatarVersionRepositoryInterface $avatarVersionRepository;

    public function __construct(AvatarVersionRepositoryInterface $avatarVersionRepository)
    {
        $this->avatarVersionRepository = $avatarVersionRepository;
    }

    public function getVersionsByAvatarId(int $id_avatar): array
    {
        return $this->avatarVersionRepository->findByAvatarId($id_avatar) ?? [];
    }

    public function createVersion(array $avatarVersionData): int
    {
        return $this->avatarVersionRepository->create($avatarVersionData);
    }

    public function levelUp(LevelUpDTO $dto): bool
    {
        return $this->avatarVersionRepository->levelUp($dto->id_avatar_version);
    }
}
