<?php

namespace alt\core\services;

use alt\core\repositories\AvatarRepositoryInterface;

class AvatarService implements AvatarServiceInterface
{
    private AvatarRepositoryInterface $avatarRepository;

    public function __construct(AvatarRepositoryInterface $avatarRepository)
    {
        $this->avatarRepository = $avatarRepository;
    }

    public function getAvatarByUserId(string $userId): array
    {
        $avatar = $this->avatarRepository->findByUserId($userId);
        
        if (!$avatar) {
            throw new \Exception("Avatar not found", 404);
        }

        return $avatar;
    }
}
