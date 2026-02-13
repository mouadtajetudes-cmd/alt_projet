<?php
declare(strict_types=1);

namespace alt\infra\auth\jwt;

use alt\core\application\ports\api\provider\AuthProviderInterface;
use alt\core\application\ports\api\provider\jwt\JwtManagerInterface;
use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use alt\core\domain\entities\User;

class JWTAuthProvider implements AuthProviderInterface
{
    private UserRepositoryInterface $userRepository;
    private JwtManagerInterface $jwtManager;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JwtManagerInterface $jwtManager
    ) {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user->password)) {
            return null;
        }
        
        return $user;
    }

    public function generateToken(User $user): string
    {
        $payload = $this->jwtManager->createPayload($user);
        return $this->jwtManager->encode($payload);
    }

    public function generateRefreshToken(User $user): string
    {
        $payload = $this->jwtManager->createRefreshPayload($user);
        return $this->jwtManager->encode($payload);
    }

    public function validateToken(string $token): ?array
    {
        return $this->jwtManager->decode($token);
    }
}
