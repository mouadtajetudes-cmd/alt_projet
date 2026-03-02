<?php
declare(strict_types=1);

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Message;

interface MessageRepositoryInterface
{
    public function create(Message $message): Message;
    public function findById(string $id): ?Message;
    public function findByConversationId(string $conversationId, int $limit = 50, int $skip = 0): array;
    public function markAsRead(string $messageId): bool;
    public function markConversationAsRead(string $conversationId, string $userId): bool;
}
