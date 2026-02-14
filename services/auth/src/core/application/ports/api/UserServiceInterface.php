<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\User;
use alt\core\application\ports\api\CreateUserDTO;
use alt\core\application\ports\api\UpdateUserDTO;

interface UserServiceInterface
{
    public function getAllUsers(): array;
    public function getUserById(int $id): User;
    public function createUser(CreateUserDTO $dto): User;
    public function updateUser(int $id, UpdateUserDTO $dto): User;
    public function deleteUser(int $id): bool;
}
