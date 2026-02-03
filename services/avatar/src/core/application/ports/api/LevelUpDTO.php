<?php

namespace alt\core\application\ports\api;

class LevelUpDTO
{
    public int $id_avatar_version;
    public int $new_level;

    public function __construct(int $id_avatar_version, int $new_level)
    {
        $this->id_avatar_version = $id_avatar_version;
        $this->new_level = $new_level;
    }
}
