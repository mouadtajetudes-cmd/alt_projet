<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class Conversation
{
    public ?string $id = null;
    public string $name;
    public array $participants = []; // array of user IDs
    public string $type = 'private'; // private, group
    public ?string $lastMessageId = null;
    public ?\DateTime $createdAt = null;
    public ?\DateTime $updatedAt = null;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'participants' => $this->participants,
            'type' => $this->type,
            'lastMessageId' => $this->lastMessageId,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}
