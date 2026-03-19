<?php
namespace alt\core\domain\entities;

class Media
{
    private int $idMedia;
    private string $titre;
    private string $type;

    public function __construct(int $idMedia, string $titre,$type)
    {
        $this->idMedia = $idMedia;
        $this->titre = $titre;
        $this->type=$type
    }


    public function getIdMedia(): int
    {
        return $this->idMedia;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }
    public function getType():string
    {
        return $this->type
    }
}