<?php

namespace alt\core\services;

use alt\core\repositories\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(string $userId): array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception("User not found", 404);
        }

        return $user;
    }
}
