<?php

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Avatar;

interface AvatarRepositoryInterface
{
    public function findAll(): array;
    
    public function findById(int $avatarId): ?array;
    
    public function findByUserId(int $userId): ?array;

    public function create(Avatar $avatar): array;

    public function update(int $id_avatar, Avatar $avatar): bool;
}
