<?php

namespace App\Factory;

use App\Models\Material;

class MaterialFactory
{
    public static function create(array $data): Material
    {
        return new Material($data);
    }
}
