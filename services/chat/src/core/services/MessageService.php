<?php

namespace alt\core\services;

use alt\core\repositories\MessageRepositoryInterface;

class MessageService implements MessageServiceInterface
{
    private MessageRepositoryInterface $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function getMessagesByRoom(string $roomId): array
    {
        $messages = $this->messageRepository->findByRoom($roomId);
        
        return $messages;
    }
}
