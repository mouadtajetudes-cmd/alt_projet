<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AdServiceInterface;
use alt\core\application\ports\api\CreateAdDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateAdAction
{
    public function __construct(
        private AdServiceInterface $adService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        
        $dto = new CreateAdDTO();
        $dto->titre = $data['titre'];
        $dto->description = $data['description'] ?? '';
        $dto->image = $data['image'] ?? '';
        $dto->lien = $data['lien'] ?? '';
        
        $ad = $this->adService->createAd($dto);
        
        $response->getBody()->write(json_encode($ad));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
