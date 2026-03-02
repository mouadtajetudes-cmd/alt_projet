<?php
declare(strict_types=1);

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\MessageServiceInterface;

class GetMessagesAction
{
    private MessageServiceInterface $messageService;

    public function __construct(MessageServiceInterface $messageService)
    {
        $this->messageService = $messageService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        $params = $request->getQueryParams();
        $conversationId = $params['conversationId'] ?? null;
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 50);

        if (!$conversationId) {
            $response->getBody()->write(json_encode([
                'error' => 'conversationId is required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $messages = $this->messageService->getMessagesByConversation($conversationId, $page, $limit);
            
            $response->getBody()->write(json_encode(array_map(fn($m) => $m->toArray(), $messages)));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}

