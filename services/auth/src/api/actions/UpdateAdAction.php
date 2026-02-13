<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AdServiceInterface;
use alt\core\application\ports\api\UpdateAdDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateAdAction
{
    public function __construct(
        private AdServiceInterface $adService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $body = $request->getParsedBody();
        
        $dto = new UpdateAdDTO();
        $dto->titre = $body['titre'] ?? '';
        $dto->description = $body['description'] ?? '';
        $dto->image = $body['image'] ?? '';
        $dto->lien = $body['lien'] ?? '';
        $dto->actif = $body['actif'] ?? true;
        
        $ad = $this->adService->updateAd($id, $dto);
        
        $response->getBody()->write(json_encode($ad));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
