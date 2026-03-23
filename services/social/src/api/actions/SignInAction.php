<?php

namespace alt\api\actions;

use alt\api\provider\AuthProviderInterface;
use alt\core\application\ports\api\CredentialDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class SignInAction extends JsonError{

    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();

        $credential = new CredentialDTO($data['email'], $data['password']);

        try {
            $auth_dto = $this->authProvider->signIn($credential);
            return JsonError::jsonError($response,  $auth_dto,200);


         } catch (\InvalidArgumentException $e) {
            return JsonError::jsonError($response, $e->getMessage(),401);
        }

    

    }
}
