<?php
namespace alt\core\application\ports\api\auth;

use alt\core\application\ports\api\CredentialDTO;
use alt\core\application\ports\api\ProfileDTO;
use alt\core\application\ports\api\Token;


interface AuthnServiceInterface{
    public function byCredentials(CredentialDTO $credentials):ProfileDTO;
    public function getSignedInUser(Token $token):ProfileDTO;
}