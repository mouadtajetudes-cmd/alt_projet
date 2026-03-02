<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Message;

interface MessageServiceInterface
{
    public function createMessage(CreateMessageDTO $dto): Message;
    public function getMessageById(string $id): ?Message;
    public function getMessagesByConversation(string $conversationId, int $page = 1, int $limit = 50): array;
    public function markAsRead(string $messageId): bool;
}
