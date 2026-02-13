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
    public string $administrateur;
    public string $premium;
    public string $auth_provider;
    public int $points;
    public ?int $id_avatar;
    public string $created_at;
    public string $updated_at;

    public function __construct($id_utilisateur = null, $nom = '', $prenom = '', $email = '', $password = '', $telephone = '', $administrateur = null, $premium = null, $auth_provider = 'local', $points = 0, $id_avatar = null, $created_at = '', $updated_at = '')
    {
        if ($id_utilisateur !== null) {
            $this->id_utilisateur = $id_utilisateur;
        }
        if ($nom) $this->nom = $nom;
        if ($prenom) $this->prenom = $prenom;
        if ($email) $this->email = $email;
        if ($telephone) $this->telephone = $telephone;
        if ($password) $this->password = $password;
        if ($administrateur !== null) $this->administrateur = $administrateur;
        if ($premium !== null) $this->premium = $premium;
        if ($auth_provider) $this->auth_provider = $auth_provider;
        if ($points !== null) $this->points = $points;
        if ($id_avatar !== null) $this->id_avatar = $id_avatar;
        if ($created_at) $this->created_at = $created_at;
        if ($updated_at) $this->updated_at = $updated_at;
    }
}
