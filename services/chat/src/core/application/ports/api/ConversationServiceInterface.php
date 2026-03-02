<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Conversation;

interface ConversationServiceInterface
{
    public function createConversation(CreateConversationDTO $dto): Conversation;
    public function getConversationById(string $id): ?Conversation;
    public function getUserConversations(string $userId): array;
    public function getOrCreatePrivateConversation(string $userId1, string $userId2): Conversation;
}
