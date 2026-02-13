<?php

namespace alt\core\application\ports\api;

class UpdateProductDTO
{
    public int $id;
    public ?string $nom;
    public ?float $prix;
    public ?int $idCategorie;
    public ?string $description;
    public ?string $statut;
    public ?int $quantite;
    public ?array $mediaIds;

    public function __construct(
        int $id,
        ?string $nom = null,
        ?float $prix = null,
        ?int $idCategorie = null,
        ?string $description = null,
        ?string $statut = null,
        ?int $quantite = null,
        ?array $mediaIds = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->idCategorie = $idCategorie;
        $this->description = $description;
        $this->statut = $statut;
        $this->quantite = $quantite;
        $this->mediaIds = $mediaIds;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['nom'] ?? null,
            isset($data['prix']) ? (float) $data['prix'] : null,
            isset($data['id_categorie']) ? (int) $data['id_categorie'] : null,
            $data['description'] ?? null,
            $data['statut'] ?? null,
            isset($data['quantite']) ? (int) $data['quantite'] : null,
            $data['media_ids'] ?? null
        );
    }
}
