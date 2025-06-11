<?php

namespace App\Factory;

use App\Models\User;

class UserFactory
{
    public static function create(array $data): User
    {
        return new User($data);
    }
}
