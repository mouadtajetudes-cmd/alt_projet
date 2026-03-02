<?php
declare(strict_types=1);

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Conversation;

interface ConversationRepositoryInterface
{
    public function create(Conversation $conversation): Conversation;
    public function findById(string $id): ?Conversation;
    public function findByParticipant(string $userId): array;
    public function findByParticipants(array $userIds): ?Conversation;
    public function updateLastMessage(string $conversationId, string $messageId): bool;
}
