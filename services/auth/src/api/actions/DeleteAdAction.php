<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AdServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteAdAction
{
    public function __construct(
        private AdServiceInterface $adService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        
        $success = $this->adService->deleteAd($id);
        
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $success ? 'Ad deleted successfully' : 'Failed to delete ad'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
