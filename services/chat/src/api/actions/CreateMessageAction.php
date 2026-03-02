<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\MessageServiceInterface;
use alt\core\application\ports\api\CreateMessageDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateMessageAction
{
    private MessageServiceInterface $messageService;

    public function __construct(MessageServiceInterface $messageService)
    {
        $this->messageService = $messageService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $dto = new CreateMessageDTO();
        $dto->conversationId = $body['conversationId'] ?? '';
        $dto->senderId = $body['senderId'] ?? '';
        $dto->content = $body['content'] ?? '';
        $dto->type = $body['type'] ?? 'text';

        try {
            $message = $this->messageService->createMessage($dto);
            
            $response->getBody()->write(json_encode($message->toArray()));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
