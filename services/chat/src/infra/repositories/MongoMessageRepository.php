<?php

namespace alt\infra\repositories;

use alt\core\repositories\MessageRepositoryInterface;
use MongoDB\Client;

class MongoMessageRepository implements MessageRepositoryInterface
{
    private Client $mongoClient;
    private string $database;

    public function __construct(Client $mongoClient, string $database = 'chat_db')
    {
        $this->mongoClient = $mongoClient;
        $this->database = $database;
    }

    public function findByRoom(string $roomId): array
    {
        $collection = $this->mongoClient->{$this->database}->messages;
        
        $cursor = $collection->find(
            ['room_id' => $roomId],
            ['sort' => ['created_at' => 1]]
        );
        
        return iterator_to_array($cursor);
    }
}
