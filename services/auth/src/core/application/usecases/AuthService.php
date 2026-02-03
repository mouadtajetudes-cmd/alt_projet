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
        
        return [
            'token' => $token,
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
        
        $user = new User();
        $user->nom = $dto->nom;
        $user->prenom = $dto->prenom;
        $user->email = $dto->email;
        $user->telephone = $dto->telephone;
        $user->password = password_hash($dto->password, PASSWORD_BCRYPT);
        $user->administrateur = false;
        $user->premium = false;
        $user->auth_provider = 'local';
        $user->points = 0;
        $user->id_avatar = 1;
        
        $user = $this->userRepository->create($user);
        
        $token = $this->authProvider->generateToken($user);
        
        return [
            'token' => $token,
            'user' => [
                'id' => $user->id_utilisateur,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ]
        ];
    }
}
