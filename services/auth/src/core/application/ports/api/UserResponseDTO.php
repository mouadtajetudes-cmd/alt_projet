<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

use alt\core\domain\entities\User;

class UserResponseDTO
{
    public static function toArrayFull(User $user): array
    {
        return [
            'id_utilisateur' => $user->id_utilisateur,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'administrateur' => $user->administrateur,
            'premium' => $user->premium,
            'id_avatar' => $user->id_avatar ?? null,
            'created_at' => $user->created_at ?? null
        ];
    }

    public static function toArrayPublic(User $user): array
    {
        return [
            'id_utilisateur' => $user->id_utilisateur,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'id_avatar' => $user->id_avatar ?? null,
            'avatar_url' => $user->avatar_url ?? null
        ];
    }
}
