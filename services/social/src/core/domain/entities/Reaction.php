<?php

namespace alt\core\domain\entities;

class Reaction
{
    private int $idReaction;
    private string $type;
    private int $idUtilisateur;
    private Post $post;

    public function __construct(
        int $idReaction,
        string $type,
        int $idUtilisateur,
        Post $post,
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

    public function getPost(): Post
    {
        return $this->post;
    }

    }
