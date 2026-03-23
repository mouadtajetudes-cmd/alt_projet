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
        error_log("AUTH DEBUG - Email: $email");
        $user = $this->userRepository->findByEmail($email);
        error_log("AUTH DEBUG - User found: " . ($user ? "YES (ID: {$user->id_utilisateur})" : "NO"));
        
        if (!$user) {
            error_log("AUTH DEBUG - No user found");
            return null;
        }
        
        error_log("AUTH DEBUG - User object type: " . get_class($user));
        error_log("AUTH DEBUG - User has password: " . (isset($user->password) ? "YES (len: " . strlen($user->password) . ")" : "NO"));
        error_log("AUTH DEBUG - Password to verify: " . substr($password, 0, 3) . "...");
        
        $passwordValid = password_verify($password, $user->password);
        error_log("AUTH DEBUG - Password valid: " . ($passwordValid ? "YES" : "NO"));
        
        if (!$passwordValid) {
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
