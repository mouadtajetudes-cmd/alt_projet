<?php

namespace alt\core\domain\entities;

class Product
{
    private ?int $id;
    private string $nom;
    private ?string $description;
    private float $prix;
    private string $statut;
    private ?\DateTimeInterface $datePublication;
    private int $quantite;
    private int $idUtilisateur;
    private int $idCategorie;
    private ?\DateTimeInterface $createdAt;
    private ?\DateTimeInterface $updatedAt;
    private array $medias = [];

    public function __construct(
        ?int $id,
        string $nom,
        float $prix,
        int $idUtilisateur,
        int $idCategorie,
        ?string $description = null,
        string $statut = 'disponible',
        int $quantite = 0,
        ?\DateTimeInterface $datePublication = null,
        ?\DateTimeInterface $createdAt = null,
        ?\DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->idUtilisateur = $idUtilisateur;
        $this->idCategorie = $idCategorie;
        $this->description = $description;
        $this->statut = $statut;
        $this->quantite = $quantite;
        $this->datePublication = $datePublication;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getIdCategorie(): int
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(int $idCategorie): self
    {
        $this->idCategorie = $idCategorie;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getMedias(): array
    {
        return $this->medias;
    }

    public function setMedias(array $medias): self
    {
        $this->medias = $medias;
        return $this;
    }

    public function addMedia(Media $media): self
    {
        $this->medias[] = $media;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_produit' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'prix' => $this->prix,
            'statut' => $this->statut,
            'quantite' => $this->quantite,
            'date_publication' => $this->datePublication?->format('Y-m-d H:i:s'),
            'id_utilisateur' => $this->idUtilisateur,
            'id_categorie' => $this->idCategorie,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'medias' => array_map(fn($m) => $m->toArray(), $this->medias),
        ];
    }

    public static function fromArray(array $data): self
    {
        $product = new self(
            $data['id_produit'] ?? null,
            $data['nom'],
            (float) $data['prix'],
            (int) $data['id_utilisateur'],
            (int) $data['id_categorie'],
            $data['description'] ?? null,
            $data['statut'] ?? 'disponible',
            (int) ($data['quantite'] ?? 0),
            isset($data['date_publication']) ? new \DateTimeImmutable($data['date_publication']) : null,
            isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
            isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null
        );

        if (isset($data['medias']) && is_array($data['medias'])) {
            $product->setMedias(array_map([Media::class, 'fromArray'], $data['medias']));
        }

        return $product;
    }
}
