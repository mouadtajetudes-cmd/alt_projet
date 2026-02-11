<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllUsersAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $users = $this->userService->getAllUsers();
        
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
