<?php

namespace alt\core\domain\entities;

use DateTime;

class Avatar
{
    private int $id_avatar;
    private string $nom;
    private ?string $image;
    private int $id_utilisateur;
    private DateTime $created_at;

    public function __construct(int $id_avatar, string $nom, ?string $image, int $id_utilisateur, DateTime $created_at)
    {
        $this->id_avatar = $id_avatar;
        $this->nom = $nom;
        $this->image = $image;
        $this->id_utilisateur = $id_utilisateur;
        $this->created_at = $created_at;
    }

    public function getIdAvatar(): int
    {
        return $this->id_avatar;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }
}
