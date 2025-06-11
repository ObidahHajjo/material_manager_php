<?php

namespace App\Requests\Auth;

use App\Requests\Request;

class LoginRequest extends Request
{
    public function rules(): array
    {
        return [
            "email" => "required|email",
            "password" => "required|string"
        ];
    }
}
