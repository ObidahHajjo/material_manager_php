<?php

namespace App\Controllers;

use App\Requests\CaptchaRequest;
use Config\Captcha;

class CaptchaController
{

    public function validate(): void
    {
        $request = new CaptchaRequest();
        $response = $request->validated()['g-recaptcha-response'];

        if (!$response) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'CAPTCHA token misseed'
            ]);
            exit();
        }

        if (!Captcha::verifyRecaptcha($response)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'CAPTCHA verification failed.'
            ]);
            exit();
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'CAPTCHA verified successfully.'
        ]);
        exit();
    }
}
