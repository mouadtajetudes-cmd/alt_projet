<?php

namespace alt\core\application\ports\api;

interface AvatarServiceInterface
{
    public function getAvatarsByUserId(string $userId): array;
    public function createAvatar(CreateAvatarDTO $dto): int;
    public function updateAvatar(UpdateAvatarDTO $dto): bool;
}
