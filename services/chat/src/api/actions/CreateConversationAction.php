<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\ConversationServiceInterface;
use alt\core\application\ports\api\CreateConversationDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateConversationAction
{
    private ConversationServiceInterface $conversationService;

    public function __construct(ConversationServiceInterface $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $dto = new CreateConversationDTO();
        $dto->name = $body['name'] ?? 'Conversation';
        $dto->participants = $body['participants'] ?? [];
        $dto->type = $body['type'] ?? 'private';

        try {
            $conversation = $this->conversationService->createConversation($dto);
            
            $response->getBody()->write(json_encode($conversation->toArray()));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
