<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class User
{
    public int $id_utilisateur;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $telephone;
    public string $password;
    public bool $administrateur;
    public bool $premium;
    public string $auth_provider;
    public int $points;
    public ?int $id_avatar;
    public string $created_at;
    public string $updated_at;
}
