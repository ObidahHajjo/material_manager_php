<?php

namespace App\Requests\Auth;

use App\Requests\Request;


class ForgetPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            "email" => "required|email"
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
