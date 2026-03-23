<?php
namespace alt\core\application\ports\api;
use alt\core\application\ports\api\ProfileDTO;

class AuthDTO{
    private string $email;
    private string $password;
    private ProfileDTO $profile;
    public function __construct(
         string $email,
        string $password,
        ProfileDTO $profile
    ){
            $this->email = $email;
            $this->password = $password;
            $this->profile = $profile;
    }
}