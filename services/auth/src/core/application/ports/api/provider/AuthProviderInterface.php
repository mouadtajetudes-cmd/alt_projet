<?php
declare(strict_types=1);

namespace alt\core\application\ports\api\provider;

use alt\core\domain\entities\User;

interface AuthProviderInterface
{
    public function authenticate(string $email, string $password): ?User;
    public function generateToken(User $user): string;
    public function validateToken(string $token): ?array;
}
