<?php
namespace alt\core\domain\entities;

class Like{
    protected int $id_like;
    protected int $id_utilisateur;
    protected int $id_post;
    public function __construct(int $id_like, int $id_utilisateur, int $id_post) {
        $this->id_like = $id_like;
        $this->id_utilisateur = $id_utilisateur;
        $this->id_post = $id_post;
    }
    public function getIdLike(): int{
        return $this->id_like;
    }
    public function getIdUtilisateur(): int{  return $this->id_utilisateur;}     
   public function getIdPost(): int{return $this->id_post;}
}