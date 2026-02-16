<?php

namespace alt\core\domain\entities;

class Reaction
{
    private int $idReaction;
    private string $type;
    private int $idUtilisateur;
    private int $post;

    public function __construct(
        int $idReaction,
        string $type,
        int $idUtilisateur,
        int $post,
    ) {
        $this->idReaction = $idReaction;
        $this->type = $type;
        $this->idUtilisateur = $idUtilisateur;
        $this->post = $post;
    }


    public function getIdReaction(): int
    {
        return $this->idReaction;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getPost(): int
    {
        return $this->post;
    }

    }
