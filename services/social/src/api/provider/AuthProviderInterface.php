<?php

namespace alt\api\provider;

use alt\core\application\ports\api\AuthDTO;
use alt\core\application\ports\api\CredentialDTO;

interface AuthProviderInterface{
     /**
    * @return AuthDTO[]
    */

    public function signIn(CredentialDTO $credential):AuthDTO;
}