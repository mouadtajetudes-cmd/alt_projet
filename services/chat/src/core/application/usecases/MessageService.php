<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\MessageServiceInterface;
use alt\core\application\ports\api\CreateMessageDTO;
use alt\core\application\ports\spi\repositoryInterfaces\MessageRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\ConversationRepositoryInterface;
use alt\core\domain\entities\Message;

class MessageService implements MessageServiceInterface
{
    private MessageRepositoryInterface $messageRepository;
    private ConversationRepositoryInterface $conversationRepository;

    public function __construct(
        MessageRepositoryInterface $messageRepository,
        ConversationRepositoryInterface $conversationRepository
    ) {
        $this->messageRepository = $messageRepository;
        $this->conversationRepository = $conversationRepository;
    }

    public function createMessage(CreateMessageDTO $dto): Message
    {
        $message = new Message();
        $message->conversationId = $dto->conversationId;
        $message->senderId = $dto->senderId;
        $message->content = $dto->content;
        $message->type = $dto->type;

        $createdMessage = $this->messageRepository->create($message);
        
        $this->conversationRepository->updateLastMessage($dto->conversationId, $createdMessage->id);

        return $createdMessage;
    }

    public function getMessageById(string $id): ?Message
    {
        return $this->messageRepository->findById($id);
    }

    public function getMessagesByConversation(string $conversationId, int $page = 1, int $limit = 50): array
    {
        $skip = ($page - 1) * $limit;
        return $this->messageRepository->findByConversationId($conversationId, $limit, $skip);
    }

    public function markAsRead(string $messageId): bool
    {
        return $this->messageRepository->markAsRead($messageId);
    }
}
