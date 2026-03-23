<?php

namespace alt\core\domain\entities;

class AvatarVersion
{
    private int $id_avatar_version;
    private ?string $surnom;
    private int $level;
    private int $id_avatar;
    private int $id_niveau;

    public function __construct(int $id_avatar_version, ?string $surnom, int $level, int $id_avatar, int $id_niveau)
    {
        $this->id_avatar_version = $id_avatar_version;
        $this->surnom = $surnom;
        $this->level = $level;
        $this->id_avatar = $id_avatar;
        $this->id_niveau = $id_niveau;
    }

    public function getIdAvatarVersion(): int
    {
        return $this->id_avatar_version;
    }

    public function getSurnom(): ?string
    {
        return $this->surnom;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getIdAvatar(): int
    {
        return $this->id_avatar;
    }

    public function getIdNiveau(): int
    {
        return $this->id_niveau;
    }
}
