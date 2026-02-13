<?php
declare(strict_types=1);

namespace alt\infra\auth\jwt;

use alt\core\application\ports\api\provider\jwt\JwtManagerInterface;
use alt\core\domain\entities\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTManager implements JwtManagerInterface
{
    private string $secret;
    private string $algorithm = 'HS256';
    private int $expirationDays = 7;
    private int $refreshExpirationDays = 30;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function decode(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return json_decode(json_encode($decoded), true);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function createPayload(User $user): array
    {
        $now = time();

        return [
            'iss' => 'alt-auth',
            'iat' => $now,
            'exp' => $now + ($this->expirationDays * 24 * 60 * 60),
            'sub' => (string) $user->id_utilisateur,
            'user' => [
                'id' => $user->id_utilisateur,
                'email' => $user->email,
                'administrateur' => $user->administrateur,
                'premium' => $user->premium
            ]
        ];
    }

    public function createRefreshPayload(User $user): array
    {
        $now = time();
        
        return [
            'iss' => 'alt-auth',
            'iat' => $now,
            'exp' => $now + ($this->refreshExpirationDays * 24 * 60 * 60),
            'sub' => (string) $user->id_utilisateur,
            'type' => 'refresh'
        ];
    }
}
