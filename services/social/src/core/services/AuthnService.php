<?php
namespace alt\core\services;
use alt\api\provider\jwt\JwtManager;
use alt\core\application\ports\api\auth\AuthnServiceInterface;
use alt\core\application\ports\api\CredentialDTO;
use alt\core\application\ports\api\ProfileDTO;
use alt\core\application\ports\api\Token;
use alt\core\repositories\UserRepository;

class AuthnService implements AuthnServiceInterface{
    private UserRepository $userRepository;
    private JwtManager $jwtDecoder;
public function __construct(
    UserRepository $userRepository,
    JwtManager $jwtDecoder
) {
    $this->userRepository = $userRepository;
    $this->jwtDecoder = $jwtDecoder;
}
public function byCredentials(CredentialDTO $credentials):ProfileDTO
{
    try {
        return $this->userRepository->byCredentials($credentials);
    } catch (\Exception $e) {
        throw new \Exception('credentials invalides');
    }
}
    public function getSignedInUser(Token $token):ProfileDTO {
        $payload=$this->jwtDecoder->validate($token->getToken());
        $user=$this->userRepository->findById($payload['userId']);
        return new ProfileDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getIsAdmin()
               );
    }
}