<?php

namespace alt\core\application\ports\api;

class CreateAvatarDTO
{
    public string $nom;
    public ?string $image;
    public int $id_utilisateur;

    public function __construct(string $nom, ?string $image, int $id_utilisateur)
    {
        $this->nom = $nom;
        $this->image = $image;
        $this->id_utilisateur = $id_utilisateur;
    }
}
