<?php
declare(strict_types=1);

namespace alt\core\application\ports\api\provider\jwt;

use alt\core\domain\entities\User;

interface JwtManagerInterface
{
    public function encode(array $payload): string;
    public function decode(string $token): ?array;
    public function createPayload(User $user): array;
}
