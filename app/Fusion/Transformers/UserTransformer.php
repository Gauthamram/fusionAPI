<?php

namespace App\Fusion\Transformers;

class UserTransformer extends Transformer
{
    public function transform($user)
    {
        return [
            'name' => $user['name'],
            'email' => $user['email'],
            'id' => $user['id'],
            'role' => $user['roles'],
            'role_id' => $user['role_id']
        ];
    }
}
