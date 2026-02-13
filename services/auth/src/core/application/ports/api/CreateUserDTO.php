<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class CreateUserDTO
{
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public ?string $telephone;
    public mixed $administrateur;
    public mixed $premium;

    public function __construct(
        string $nom,
        string $prenom,
        string $email,
        string $password,
        ?string $telephone = null,
        mixed $administrateur = false,
        mixed $premium = false
    ) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->telephone = $telephone;
        $this->administrateur = $administrateur;
        $this->premium = $premium;
    }
}
