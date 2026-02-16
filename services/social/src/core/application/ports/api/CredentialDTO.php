<?php
namespace alt\core\application\ports\api;


class CredentialDTO{
    public string $email;
    public string $password;

    public function  __construct (string $email, string $password){
        $this->email=$email;
        $this->password=$password;
    }

}