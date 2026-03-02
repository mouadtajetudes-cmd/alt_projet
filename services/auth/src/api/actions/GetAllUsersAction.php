<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\UserResponseDTO;
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
        
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = UserResponseDTO::toArrayPublic($user);
        }
        
        $response->getBody()->write(json_encode($usersArray));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
