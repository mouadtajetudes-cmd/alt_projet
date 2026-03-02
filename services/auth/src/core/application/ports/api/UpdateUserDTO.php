<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class UpdateUserDTO
{
    public ?string $nom;
    public ?string $prenom;
    public ?string $email;
    public ?string $telephone;
    public ?string $bio;
    public ?string $statut_personnalise;
    public mixed $administrateur;
    public mixed $premium;
    public ?string $password;

    public function __construct(
        ?string $nom = null,
        ?string $prenom = null,
        ?string $email = null,
        ?string $telephone = null,
        ?string $bio = null,
        ?string $statut_personnalise = null,
        mixed $administrateur = null,
        mixed $premium = null,
        ?string $password = null
    ) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->bio = $bio;
        $this->statut_personnalise = $statut_personnalise;
        $this->administrateur = $administrateur;
        $this->premium = $premium;
        $this->password = $password;
    }
}
