<?php

namespace alt\core\application\ports\api;

interface AvatarServiceInterface
{
    public function getAllAvatars(): array;
    
    public function getAvatarsByUserId(string $userId): array;
    public function createAvatar(CreateAvatarDTO $dto): array;
    public function updateAvatar(UpdateAvatarDTO $dto): bool;
}
