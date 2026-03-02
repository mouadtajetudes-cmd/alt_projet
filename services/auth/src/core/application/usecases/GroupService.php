<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\CreateGroupDTO;
use alt\core\application\ports\spi\repositoryInterfaces\GroupRepositoryInterface;
use alt\core\domain\entities\Group;

class GroupService implements GroupServiceInterface
{
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepository
    ) {
        $this->groupRepository = $groupRepository;
    }

    public function getAllGroups(): array
    {
        return $this->groupRepository->findAll();
    }

    public function getGroupById(int $id): Group
    {
        return $this->groupRepository->findById($id);
    }

    public function createGroup(CreateGroupDTO $dto): Group
    {
        $group = new Group();
        $group->nom = $dto->nom;
        $group->description = $dto->description;
        $group->niveau = $dto->niveau;
        
        return $this->groupRepository->create($group);
    }

    public function updateGroup(Group $group): Group
    {
        return $this->groupRepository->update($group);
    }

    public function addMember(int $groupId, int $userId, string $role = 'member'): bool
    {
        return $this->groupRepository->addMember($groupId, $userId, $role);
    }

    public function getMembers(int $groupId): array
    {
        return $this->groupRepository->getMembers($groupId);
    }

    public function getUserGroups(int $userId): array
    {
        return $this->groupRepository->getUserGroups($userId);
    }

    public function getGroupMembers(int $groupId): array
    {
        return $this->groupRepository->getGroupMembers($groupId);
    }

    public function removeMember(int $groupId, int $userId): bool
    {
        return $this->groupRepository->removeMember($groupId, $userId);
    }

    public function getMemberRole(int $groupId, int $userId): ?string
    {
        return $this->groupRepository->getMemberRole($groupId, $userId);
    }
}
