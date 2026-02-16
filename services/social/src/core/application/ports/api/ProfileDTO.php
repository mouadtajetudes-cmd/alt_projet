<?php

namespace alt\core\application\ports\api;

class ProfileDTO{
    public int $id;
    public string $email;
    public bool $isAdmin;

    public function __construct(int $id,string $email,bool $isAdmin){
        $this->id=$id;
        $this->email=$email;
        $this->isAdmin=$isAdmin;
    }
    public function getId(){
        return $this->id;
    }
    public function getEMail(){
        return $this->email;
    }
    public function getIsAdmin(){
        return $this->isAdmin;
    }
}