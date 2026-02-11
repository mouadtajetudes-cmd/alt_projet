<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AdServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllAdsAction
{
    public function __construct(
        private AdServiceInterface $adService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ads = $this->adService->getAllAds();
        
        $response->getBody()->write(json_encode($ads));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
