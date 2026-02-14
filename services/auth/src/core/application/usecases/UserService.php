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
        
        // Ensure administrateur and premium are stored as string 'true' or 'false'
        $administrateur = ($dto->administrateur === true || $dto->administrateur === 'true' || $dto->administrateur === 1 || $dto->administrateur === '1') ? 'true' : 'false';
        $premium = ($dto->premium === true || $dto->premium === 'true' || $dto->premium === 1 || $dto->premium === '1') ? 'true' : 'false';
        
        $user = new User(
            null,
            $dto->nom,
            $dto->prenom,
            $dto->email,
            $hashedPassword,
            $dto->telephone,
            $administrateur,
            $premium,
            'local',
            0,
            1
        );
        
        return $this->userRepository->create($user);
    }

    public function updateUser(int $id, UpdateUserDTO $dto): User
    {
        $updateData = [];
        
        if ($dto->nom !== null) {
            $updateData['nom'] = $dto->nom;
        }
        if ($dto->prenom !== null) {
            $updateData['prenom'] = $dto->prenom;
        }
        if ($dto->email !== null) {
            $updateData['email'] = $dto->email;
        }
        if ($dto->telephone !== null) {
            $updateData['telephone'] = $dto->telephone;
        }
        if ($dto->administrateur !== null) {
            // Ensure administrateur is stored as string 'true' or 'false'
            $updateData['administrateur'] = ($dto->administrateur === true || $dto->administrateur === 'true' || $dto->administrateur === 1 || $dto->administrateur === '1') ? 'true' : 'false';
        }
        if ($dto->premium !== null) {
            // Ensure premium is stored as string 'true' or 'false'
            $updateData['premium'] = ($dto->premium === true || $dto->premium === 'true' || $dto->premium === 1 || $dto->premium === '1') ? 'true' : 'false';
        }
        if (!empty($dto->password)) {
            $updateData['password'] = password_hash($dto->password, PASSWORD_BCRYPT);
        }
        
        return $this->userRepository->update($id, $updateData);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function updateUserAvatar(int $id, string $avatarUrl): bool
    {
        return $this->userRepository->update($id, ['avatar_url' => $avatarUrl]) !== null;
    }

    public function updateOnlineStatus(int $id, bool $isOnline): bool
    {
        $data = [
            'is_online' => $isOnline,
            'last_seen' => date('Y-m-d H:i:s')
        ];
        return $this->userRepository->update($id, $data) !== null;
    }
}
