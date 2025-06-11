<?php

namespace App\Requests\Auth;

use App\Requests\Request;

class ResetPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            "token" => "string",
            "password" => "string",
            "password_confirmation" => "string"
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
