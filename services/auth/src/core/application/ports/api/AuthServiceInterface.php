<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

interface AuthServiceInterface
{
    public function login(LoginDTO $dto): array;
    public function register(CreateUserDTO $dto): array;
}
