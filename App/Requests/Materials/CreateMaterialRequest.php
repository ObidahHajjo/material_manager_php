<?php

namespace App\Requests\Materials;

use App\Requests\Request;

class CreateMaterialRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'category' => 'required|string',
            'quantity' => 'required|int',
            'status' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
