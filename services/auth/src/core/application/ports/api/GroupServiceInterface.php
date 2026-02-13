<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Group;

interface GroupServiceInterface
{
    public function getAllGroups(): array;
    public function getGroupById(int $id): Group;
    public function createGroup(CreateGroupDTO $dto): Group;
    public function addMember(int $groupId, int $userId): bool;
    public function getMembers(int $groupId): array;
    public function getUserGroups(int $userId): array;
    public function getGroupMembers(int $groupId): array;
    public function removeMember(int $groupId, int $userId): bool;
}
