<?php

namespace Config;

class Captcha
{
    public static function verifyRecaptcha(string $token): bool
    {
        $secretKey = '6LdJ2VwrAAAAAFHpN_AAGbTqiUhcPoQ9m5HCD59I'; // Remplace par ta vraie clé secrète Google

        // Préparer la requête POST à Google
        $response = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret='
                . $secretKey . '&response=' . urlencode($token)
        );

        // Convertir la réponse JSON
        $responseData = json_decode($response);

        // Retourner vrai si "success" est vrai
        return isset($responseData->success) && $responseData->success;
    }
}
