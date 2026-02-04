<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\User;

interface UserServiceInterface
{
    public function getAllUsers(): array;
    public function getUserById(int $id): User;
    public function createUser(CreateUserDTO $dto): User;
}
