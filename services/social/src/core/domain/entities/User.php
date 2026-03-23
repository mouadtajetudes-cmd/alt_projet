<?php

namespace alt\core\domain\entities;

class User
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $password;
    private bool $isAdmin;
    private 

    public function __construct(int $id, string $nom,string $prenom, string $email, string $password, bool $isAdmin = false)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function getIsAdmin(): bool{
        return $this->isAdmin;
    }
     public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

}