<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\application\ports\api\LoginDTO;
use alt\core\application\ports\api\CreateUserDTO;
use alt\core\application\ports\api\provider\AuthProviderInterface;
use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use alt\core\domain\entities\User;

class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private AuthProviderInterface $authProvider;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AuthProviderInterface $authProvider
    ) {
        $this->userRepository = $userRepository;
        $this->authProvider = $authProvider;
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->authProvider->authenticate($dto->email, $dto->password);
        
        if (!$user) {
            throw new \Exception('Invalid credentials', 401);
        }
        
        $token = $this->authProvider->generateToken($user);
        $refreshToken = $this->authProvider->generateRefreshToken($user);
        
        return [
            'token' => $token,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->id_utilisateur,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'administrateur' => $user->administrateur,
                'premium' => $user->premium
            ]
        ];
    }

    public function register(CreateUserDTO $dto): array
    {
        $existing = $this->userRepository->findByEmail($dto->email);
        if ($existing) {
            throw new \Exception('Email already exists', 400);
        }
        
        $hashedPassword = password_hash($dto->password, PASSWORD_BCRYPT);
        
        $user = new User(
            null,
            $dto->nom,
            $dto->prenom,
            $dto->email,
            $hashedPassword,
            $dto->telephone,
            false,
            false,
            'local',
            0,
            1
        );
        
        $user = $this->userRepository->create($user);
        
        $token = $this->authProvider->generateToken($user);
        $refreshToken = $this->authProvider->generateRefreshToken($user);
        
        return [
            'token' => $token,
            'refresh_token' => $refreshToken,
            'user' => [
                'id' => $user->id_utilisateur,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ]
        ];
    }

    public function refreshToken(string $refreshToken): array
    {
        $payload = $this->authProvider->validateToken($refreshToken);
        
        if (!$payload || !isset($payload['type']) || $payload['type'] !== 'refresh') {
            throw new \Exception('Invalid refresh token', 401);
        }
        
        $userId = (int) $payload['sub'];
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('User not found', 401);
        }
        
        $newToken = $this->authProvider->generateToken($user);
        $newRefreshToken = $this->authProvider->generateRefreshToken($user);
        
        return [
            'token' => $newToken,
            'refresh_token' => $newRefreshToken,
            'user' => [
                'id' => $user->id_utilisateur,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'administrateur' => $user->administrateur,
                'premium' => $user->premium
            ]
        ];
    }
}
