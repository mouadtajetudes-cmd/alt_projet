<?php
namespace alt\core\application\ports\api;

class CreateLikeDTO
{
    public int $id_post;
    public int $id_utilisateur;

    public function __construct(int $id_utilisateur, int $id_post)
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->id_post = $id_post;
    }

    public function getIdPost(): int
    {
        return $this->id_post;
    }

    public function getIdUtilisateur(): int
    {
        return $this->id_utilisateur;
    }
}