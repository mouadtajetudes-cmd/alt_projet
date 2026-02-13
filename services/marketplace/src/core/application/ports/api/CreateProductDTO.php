<?php

namespace alt\core\application\ports\api;

class CreateProductDTO
{
    public string $nom;
    public float $prix;
    public int $idUtilisateur;
    public int $idCategorie;
    public ?string $description;
    public string $statut;
    public int $quantite;
    public array $mediaIds;

    public function __construct(
        string $nom,
        float $prix,
        int $idUtilisateur,
        int $idCategorie,
        ?string $description = null,
        string $statut = 'disponible',
        int $quantite = 0,
        array $mediaIds = []
    ) {
        $this->nom = $nom;
        $this->prix = $prix;
        $this->idUtilisateur = $idUtilisateur;
        $this->idCategorie = $idCategorie;
        $this->description = $description;
        $this->statut = $statut;
        $this->quantite = $quantite;
        $this->mediaIds = $mediaIds;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nom'],
            (float) $data['prix'],
            (int) $data['id_utilisateur'],
            (int) $data['id_categorie'],
            $data['description'] ?? null,
            $data['statut'] ?? 'disponible',
            (int) ($data['quantite'] ?? 0),
            $data['media_ids'] ?? []
        );
    }
}
