<?php

namespace alt\core\application\ports\api;

final class UpdatePostDTO
{   
    private ?bool $isDraft;
    private ?string $description;
    private ?array $file;

    /**
     * @param string|null $description Le texte du post
     * @param array|null $file Tableau associatif contenant :
     *   - name : nom du fichier
     *   - type : type MIME (image/video)
     *   - tmp_name : chemin temporaire du fichier
     *   - folder : dossier de destination ('images' ou 'videos')
     */
    public function __construct(?bool $isDraft,?string $description = null, ?array $file = null)
    { 
        $this->isDraft = $isDraft;
        $this->description = $description;
        $this->file = $file;
    }
    
    public function getIsDraft(): ?bool{
        return $this->isDraft;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getFile(): ?array
    {
        return $this->file;
    }

    public function setFile(?array $file): void
    {
        $this->file = $file;
    }
}