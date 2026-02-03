<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\services\MessageServiceInterface;

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
        
        $roomId = $args['roomId'] ?? null;

        try {
            $messages = $this->messageService->getMessagesByRoom($roomId);
            
            $response->getBody()->write(json_encode([
                'type' => 'collection',
                'messages' => $messages
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 404,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}
