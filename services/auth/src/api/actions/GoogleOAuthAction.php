<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AuthServiceInterface;

use League\OAuth2\Client\Provider\Google;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use alt\core\domain\entities\User;
use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;

class GoogleOAuthAction
{
    private AuthServiceInterface $authService;
    private ContainerInterface $container;

    public function __construct(AuthServiceInterface $authService, ContainerInterface $container)
    {
        $this->authService = $authService;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $provider = new Google([
            'clientId'     => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
            'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
            'redirectUri'  => $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost:5173/auth/google/callback',
        ]);

        $params = $request->getQueryParams();

        if (!isset($params['code'])) {
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            
            $response->getBody()->write(json_encode([
                'authUrl' => $authUrl
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        if (empty($params['state']) || (isset($_SESSION['oauth2state']) && $params['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            
            $response->getBody()->write(json_encode([
                'error' => 'Invalid state'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $params['code']
            ]);

            $googleUser = $provider->getResourceOwner($token);
            $userData = $googleUser->toArray();

            $userRepo = $this->container->get(UserRepositoryInterface::class);
            $existingUser = $userRepo->findByEmail($userData['email']);

            if ($existingUser) {
                $result = $this->authService->generateTokensForUser($existingUser);

                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $user = new User(
                null,
                $userData['given_name'] ?? $userData['name'],
                $userData['family_name'] ?? '',
                $userData['email'],
                '',      // password
                null,    // telephone
                'false', // administrateur
                'false', // premium
                'google',
                0,
                null
            );

            $createdUser = $userRepo->create($user);
            
            $result = $this->authService->generateTokensForUser($createdUser);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'OAuth failed',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
