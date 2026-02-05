<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\ConversationRepositoryInterface;
use alt\core\domain\entities\Conversation;
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

class MongoConversationRepository implements ConversationRepositoryInterface
{
    private $collection;

    public function __construct(Client $mongoClient, string $database)
    {
        $this->collection = $mongoClient->selectCollection($database, 'conversations');
    }

    public function create(Conversation $conversation): Conversation
    {
        $conversation->createdAt = new \DateTime();
        $conversation->updatedAt = new \DateTime();

        $document = [
            'name' => $conversation->name,
            'participants' => $conversation->participants,
            'type' => $conversation->type,
            'lastMessageId' => $conversation->lastMessageId,
            'createdAt' => $conversation->createdAt,
            'updatedAt' => $conversation->updatedAt
        ];

        $result = $this->collection->insertOne($document);
        $conversation->id = (string) $result->getInsertedId();

        return $conversation;
    }

    public function findById(string $id): ?Conversation
    {
        try {
            $document = $this->collection->findOne(['_id' => new ObjectId($id)]);
            
            if (!$document) {
                return null;
            }

            return $this->documentToConversation($document);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findByParticipant(string $userId): array
    {
        $cursor = $this->collection->find(
            ['participants' => $userId],
            ['sort' => ['updatedAt' => -1]]
        );

        $conversations = [];
        foreach ($cursor as $document) {
            $conversations[] = $this->documentToConversation($document);
        }

        return $conversations;
    }

    public function findByParticipants(array $userIds): ?Conversation
    {
        sort($userIds);
        
        $document = $this->collection->findOne([
            'participants' => ['$all' => $userIds, '$size' => count($userIds)],
            'type' => 'private'
        ]);

        if (!$document) {
            return null;
        }

        return $this->documentToConversation($document);
    }

    public function updateLastMessage(string $conversationId, string $messageId): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($conversationId)],
                ['$set' => ['lastMessageId' => $messageId, 'updatedAt' => new \DateTime()]]
            );

            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function documentToConversation($document): Conversation
    {
        $conversation = new Conversation();
        $conversation->id = (string) $document['_id'];
        $conversation->name = $document['name'];
        $conversation->participants = $document['participants'];
        $conversation->type = $document['type'] ?? 'private';
        $conversation->lastMessageId = $document['lastMessageId'] ?? null;
        $conversation->createdAt = $document['createdAt']->toDateTime();
        $conversation->updatedAt = $document['updatedAt']->toDateTime();

        return $conversation;
    }
}
