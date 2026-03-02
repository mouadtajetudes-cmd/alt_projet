<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use alt\core\application\ports\api\LevelServiceInterface;

class GetLevelsAction
{
    private LevelServiceInterface $levelService;

    public function __construct(LevelServiceInterface $levelService)
    {
        $this->levelService = $levelService;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        
        try {
            $levels = $this->levelService->getAllLevels();
            
            $response->getBody()->write(json_encode([
                'type' => 'collection',
                'levels' => $levels
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 500,
                'message' => $e->getMessage()
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
