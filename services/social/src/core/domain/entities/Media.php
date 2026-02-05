<?php
namespace alt\core\domain\entities;

class Media
{
    private int $idMedia;
    private string $titre;

    public function __construct(int $idMedia, string $titre)
    {
        $this->idMedia = $idMedia;
        $this->titre = $titre;
    }


    public function getIdMedia(): int
    {
        return $this->idMedia;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }
}