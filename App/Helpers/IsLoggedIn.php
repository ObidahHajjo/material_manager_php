<?php

namespace App\Helpers;

use Config\Session;

class IsLoggedIn
{
    private static ?Session $session = null;

    public static function isLoggedIn(): void
    {
        if (self::$session === null) {
            self::$session = Session::getInstance();
        }

        if (!self::$session->isset('isLoggedIn')) {
            header("Location: /");
            exit();
        }
    }
}
