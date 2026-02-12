<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\CreateUserDTO;
use alt\core\application\ports\api\UpdateUserDTO;
use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use alt\core\domain\entities\User;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUserById(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(CreateUserDTO $dto): User
    {
        $hashedPassword = password_hash($dto->password, PASSWORD_BCRYPT);
        
        $user = new User(
            null,
            $dto->nom,
            $dto->prenom,
            $dto->email,
            $hashedPassword,
            $dto->telephone,
            $dto->administrateur,
            $dto->premium,
            'local',
            0,
            1
        );
        
        return $this->userRepository->create($user);
    }

    public function updateUser(int $id, UpdateUserDTO $dto): User
    {
        $updateData = [];
        
        $updateData['nom'] = $dto->nom;
        $updateData['prenom'] = $dto->prenom;
        $updateData['email'] = $dto->email;
        $updateData['telephone'] = $dto->telephone;
        $updateData['administrateur'] = $dto->administrateur;
        $updateData['premium'] = $dto->premium;
        
        return $this->userRepository->update($id, $updateData);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
