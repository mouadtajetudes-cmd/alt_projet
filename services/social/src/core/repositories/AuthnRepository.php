<?php

namespace alt\core\repositories;

use alt\core\application\ports\api\CredentialDTO;
use alt\core\application\ports\api\ProfileDTO;
use alt\core\domain\entities\User;

interface AuthnRepository{
    public function register(CredentialDTO $credentials, int $role):ProfileDTO;
    public function byCredentials(CredentialDTO $credentials):ProfileDTO;
    public function byEmail(string $email):User;
}