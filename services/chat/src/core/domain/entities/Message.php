<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class Message
{
    public ?string $id = null;
    public string $conversationId;
    public string $senderId;
    public string $content;
    public ?string $type = 'text'; // text, image, file
    public bool $isRead = false;
    public ?\DateTime $createdAt = null;
    public ?\DateTime $updatedAt = null;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'conversationId' => $this->conversationId,
            'senderId' => $this->senderId,
            'content' => $this->content,
            'type' => $this->type,
            'isRead' => $this->isRead,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}
