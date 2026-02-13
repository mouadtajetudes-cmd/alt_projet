<?php

namespace alt\core\domain\entities;

class Level
{
    private int $id_niveau;
    private string $nom;
    private ?string $description;
    private int $points;

    public function __construct(int $id_niveau, string $nom, ?string $description, int $points)
    {
        $this->id_niveau = $id_niveau;
        $this->nom = $nom;
        $this->description = $description;
        $this->points = $points;
    }

    public function getIdNiveau(): int
    {
        return $this->id_niveau;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPoints(): int
    {
        return $this->points;
    }
}
