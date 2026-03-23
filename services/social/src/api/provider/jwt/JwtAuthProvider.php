<?php
namespace alt\api\provider\jwt;

use alt\api\provider\AuthProviderInterface;
use alt\core\application\ports\api\auth\AuthnServiceInterface;
use alt\core\application\ports\api\AuthDTO;
use alt\core\application\ports\api\CredentialDTO;

class JwtAuthProvider implements  AuthProviderInterface{

    private AuthnServiceInterface $authService;
    private JwtManagerInterface $jwtManager;
    public function __construct(AuthnServiceInterface $authService, JwtManagerInterface $jwtManager){
        $this->authService=$authService;
        $this->jwtManager=$jwtManager;
    }
    public function signIn(CredentialDTO $credentials):AuthDTO{
        try{
        $user=$this->authService->byCredentials($credentials);
        $accessToken=$this->jwtManager->create([
            'id'=>$user->id,
            'email'=>$user->email,
            'isadmin'=>$user->isAdmin,
        ],JwTManagerInterface::ACCESS_TOKEN);
        $refreshToken=$this->jwtManager->create([
            'id'=>$user->id,
            'email'=>$user->email,
            'isadmin'=>$user->isAdmin,
        ],JwTManagerInterface::REFRESH_TOKEN);
       
        $auth=new AuthDTO(
            $accessToken,
            $refreshToken,
            $user,
        );
        return $auth;

    }catch(\Exception $e){
        throw new \Exception('credentials invalides'.$e->getMessage());
    }
    }
}