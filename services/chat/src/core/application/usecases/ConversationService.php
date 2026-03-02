<?php
declare(strict_types=1);

namespace alt\core\application\usecases;

use alt\core\application\ports\api\ConversationServiceInterface;
use alt\core\application\ports\api\CreateConversationDTO;
use alt\core\application\ports\spi\repositoryInterfaces\ConversationRepositoryInterface;
use alt\core\domain\entities\Conversation;

class ConversationService implements ConversationServiceInterface
{
    private ConversationRepositoryInterface $conversationRepository;

    public function __construct(ConversationRepositoryInterface $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function createConversation(CreateConversationDTO $dto): Conversation
    {
        $conversation = new Conversation();
        $conversation->name = $dto->name;
        $conversation->participants = $dto->participants;
        $conversation->type = $dto->type;

        return $this->conversationRepository->create($conversation);
    }

    public function getConversationById(string $id): ?Conversation
    {
        return $this->conversationRepository->findById($id);
    }

    public function getUserConversations(string $userId): array
    {
        return $this->conversationRepository->findByParticipant($userId);
    }

    public function getOrCreatePrivateConversation(string $userId1, string $userId2): Conversation
    {
        $userIds = [$userId1, $userId2];
        sort($userIds);

        $existing = $this->conversationRepository->findByParticipants($userIds);
        
        if ($existing) {
            return $existing;
        }

        $dto = new CreateConversationDTO();
        $dto->name = 'Private Chat';
        $dto->participants = $userIds;
        $dto->type = 'private';

        return $this->createConversation($dto);
    }
}
