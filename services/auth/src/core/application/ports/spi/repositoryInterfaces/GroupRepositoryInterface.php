<?php
declare(strict_types=1);

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Group;

interface GroupRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): Group;
    public function create(Group $group): Group;
    public function update(Group $group): Group;
    public function addMember(int $groupId, int $userId, string $role = 'member'): bool;
    public function getMembers(int $groupId): array;
    public function getUserGroups(int $userId): array;
    public function getGroupMembers(int $groupId): array;
    public function removeMember(int $groupId, int $userId): bool;
    public function getMemberRole(int $groupId, int $userId): ?string;
}
