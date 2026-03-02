<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\ConversationServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetConversationsAction
{
    private ConversationServiceInterface $conversationService;

    public function __construct(ConversationServiceInterface $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = $args['userId'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'userId is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $conversations = $this->conversationService->getUserConversations($userId);
            
            $response->getBody()->write(json_encode(array_map(fn($c) => $c->toArray(), $conversations)));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
