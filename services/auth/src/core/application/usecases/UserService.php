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
        
        return $this->userRepository->create($user);
    }

    public function updateUser(int $id, UpdateUserDTO $dto): User
    {
        $data = [
            'nom' => $dto->nom,
            'prenom' => $dto->prenom,
            'email' => $dto->email,
            'telephone' => $dto->telephone
        ];
        
        return $this->userRepository->update($id, $data);
    }
}
