<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class User
{
    public int $id_utilisateur;
    public string $nom;
    public string $prenom;
    public string $email;
    public ?string $telephone;
    public string $password;
    public string $administrateur;
    public string $premium;
    public string $auth_provider;
    public int $points;
    public ?int $id_avatar;
    public ?string $bio;
    public ?string $banner_url;
    public ?string $statut_personnalise;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct($id_utilisateur = null, $nom = '', $prenom = '', $email = '', $password = '', $telephone = null, $administrateur = null, $premium = null, $auth_provider = 'local', $points = 0, $id_avatar = null, $bio = null, $banner_url = null, $statut_personnalise = null, $created_at = null, $updated_at = null)
    {
        if ($id_utilisateur !== null) {
            $this->id_utilisateur = $id_utilisateur;
        }
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->password = $password;
        $this->administrateur = $administrateur ?? 'false';
        $this->premium = $premium ?? 'false';
        $this->auth_provider = $auth_provider ?? 'local';
        $this->points = $points ?? 0;
        $this->id_avatar = $id_avatar;
        $this->bio = $bio;
        $this->banner_url = $banner_url;
        $this->statut_personnalise = $statut_personnalise;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
