<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\application\ports\api\CreateUserDTO;
use alt\core\application\ports\api\LoginDTO;

interface AuthServiceInterface
{
    public function login(LoginDTO $dto): array;
    public function register(CreateUserDTO $dto): array;
    public function refreshToken(string $refreshToken): array;
}
