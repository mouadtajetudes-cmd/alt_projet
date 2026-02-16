<?php

namespace alt\core\application\ports\api;

class UpdateAvatarDTO
{
    public int $id_avatar;
    public ?string $nom;
    public ?string $image;

    public function __construct(int $id_avatar, ?string $nom = null, ?string $image = null)
    {
        $this->id_avatar = $id_avatar;
        $this->nom = $nom;
        $this->image = $image;
    }
}
