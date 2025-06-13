<?php

namespace App\Requests\Users;

use App\Requests\Request;

class CreateUserRequest extends Request
{
    public function rules(): array
    {
        return [
            "username" => "required|string",
            "email" => "required",
            "role" => "required|string"
        ];
    }

    public function messages(): array
    {
        return [
            "username.required" => "The username field is required.",
            "email.required" => "The email field hellllllllo mother fucker is required.",
            "role.required" => "The role field is required."
        ];
    }
}
