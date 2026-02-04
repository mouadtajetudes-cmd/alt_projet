<?php
declare(strict_types=1);

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\User;

interface UserRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): User;
    public function findByEmail(string $email): ?User;
    public function create(User $user): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
}
