<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\MessageRepositoryInterface;
use alt\core\domain\entities\Message;
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class MongoMessageRepository implements MessageRepositoryInterface
{
    private $collection;

    public function __construct(Client $mongoClient, string $database)
    {
        $this->collection = $mongoClient->selectCollection($database, 'messages');
    }

    public function create(Message $message): Message
    {
        $message->createdAt = new \DateTime();
        $message->updatedAt = new \DateTime();

        $document = [
            'conversationId' => $message->conversationId,
            'senderId' => $message->senderId,
            'content' => $message->content,
            'type' => $message->type,
            'isRead' => $message->isRead,
            'createdAt' => $message->createdAt,
            'updatedAt' => $message->updatedAt
        ];

        $result = $this->collection->insertOne($document);
        $message->id = (string) $result->getInsertedId();

        return $message;
    }

    public function findById(string $id): ?Message
    {
        try {
            $document = $this->collection->findOne(['_id' => new ObjectId($id)]);
            
            if (!$document) {
                return null;
            }

            return $this->documentToMessage($document);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByConversationId(string $conversationId, int $limit = 50, int $skip = 0): array
    {
        $cursor = $this->collection->find(
            ['conversationId' => $conversationId],
            [
                'sort' => ['createdAt' => -1],
                'limit' => $limit,
                'skip' => $skip
            ]
        );

        $messages = [];
        foreach ($cursor as $document) {
            $messages[] = $this->documentToMessage($document);
        }

        return array_reverse($messages);
    }

    public function markAsRead(string $messageId): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($messageId)],
                ['$set' => ['isRead' => true, 'updatedAt' => new \DateTime()]]
            );

            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function markConversationAsRead(string $conversationId, string $userId): bool
    {
        $result = $this->collection->updateMany(
            [
                'conversationId' => $conversationId,
                'senderId' => ['$ne' => $userId],
                'isRead' => false
            ],
            ['$set' => ['isRead' => true, 'updatedAt' => new \DateTime()]]
        );

        return $result->getModifiedCount() >= 0;
    }

    private function documentToMessage($document): Message
    {
        $message = new Message();
        $message->id = (string) $document['_id'];
        $message->conversationId = $document['conversationId'];
        $message->senderId = $document['senderId'];
        $message->content = $document['content'];
        $message->type = $document['type'] ?? 'text';
        $message->isRead = $document['isRead'] ?? false;
        
        if (isset($document['createdAt'])) {
            if ($document['createdAt'] instanceof \MongoDB\BSON\UTCDateTime) {
                $message->createdAt = $document['createdAt']->toDateTime();
            } else {
                $message->createdAt = new \DateTime();
            }
        } else {
            $message->createdAt = new \DateTime();
        }
        
        if (isset($document['updatedAt'])) {
            if ($document['updatedAt'] instanceof \MongoDB\BSON\UTCDateTime) {
                $message->updatedAt = $document['updatedAt']->toDateTime();
            } else {
                $message->updatedAt = new \DateTime();
            }
        } else {
            $message->updatedAt = $message->createdAt;
        }

        return $message;
    }
}

