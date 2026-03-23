<?php
namespace alt\api\provider;
use alt\core\application\ports\api\auth\AuthnServiceInterface;
use alt\core\application\ports\api\AuthDTO;
use alt\core\application\ports\api\CredentialDTO;
use alt\core\application\ports\api\ProfileDTO;
use alt\api\provider\jwt\JwtManagerInterface;
use alt\api\provider\AuthProviderInterface;

class AuthnProvider implements  AuthProviderInterface{
        private AuthnServiceInterface $authnService;
      private JwtManagerInterface $jwtManager;

    public function __construct(
        AuthnServiceInterface $authnService, 
        JwtManagerInterface $jwtManager
    ) {
        $this->authnService = $authnService;
        $this->jwtManager = $jwtManager;
    }


    public function signIn(CredentialDTO $credential): AuthDTO {
        $profile = $this->authnService->byCredentials($credential);
        
        $access_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
        ], JwtManagerInterface::ACCESS_TOKEN);
        
        $refresh_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
        ], JwtManagerInterface::REFRESH_TOKEN);
        
        return new AuthDTO($access_token, $refresh_token, $profile);
    }

    public function getSignedInUser(string $token): ProfileDTO {
        $payload = $this->jwtManager->validate($token);
        
        return new ProfileDTO(
            $payload['id'], 
            $payload['email'], 
            $payload['isAdmin']
        );
    }

    public function refresh(string $refreshToken): AuthDTO {
        $payload = $this->jwtManager->validate($refreshToken);
        
        $profile = new ProfileDTO(
            $payload['id'],
            $payload['email'],
            $payload['isAdmin'],
        );
        
        $access_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
        ], JwtManagerInterface::ACCESS_TOKEN);
        
        $new_refresh_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
        ], JwtManagerInterface::REFRESH_TOKEN);
        
        return new AuthDTO($access_token, $new_refresh_token, $profile);
    }

    
}