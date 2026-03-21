<?php
namespace alt\core\domain\entities;

class Post
{
    private int $idPost;
    private ?string $description;
    private ?string $titre;
    private ?string $mediaType;
    private ?string $mediaUrl;
    private int $idUtilisateur;
    private bool $isDraft;

    public function __construct(
        int $idPost,
        bool $isDraft,
        ?string $description,
        int $idUtilisateur,
        ?string $titre = null,
        ?string $mediaType = null,
        ?string $mediaUrl = null,
        )
         {
        $this->idPost = $idPost;
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
        $this->titre = $titre;
        $this->mediaType = $mediaType;
        $this->mediaUrl = $mediaUrl;
        $this->isDraft= $isDraft;
    }

    public function getIdPost(): int { return $this->idPost; }
    public function getDescription(): ?string { return $this->description; }
    public function getIdUtilisateur(): int { return $this->idUtilisateur; }
    public function getTitre(): ?string { return $this->titre; }
    public function getMediaType(): ?string { return $this->mediaType; }
    public function getMediaUrl(): ?string { return $this->mediaUrl; }
    public function getIsDraft():bool { return $this->isDraft;}
}