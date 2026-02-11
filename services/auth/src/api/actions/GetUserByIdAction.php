<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetUserByIdAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $user = $this->userService->getUserById($id);
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
