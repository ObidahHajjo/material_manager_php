<?php

namespace App\Requests;

class CaptchaRequest extends Request
{
    public function rules(): array
    {
        return [
            "g-recaptcha-response" => "required|string"
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
