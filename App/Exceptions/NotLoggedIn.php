<?php

namespace App\Exceptions;

use Exception;

class NotLoggedIn extends Exception
{
    private string $errorMessage = "You are not connected. You will be redirected to login.";
    private int $errorCode = 401;
    private string $url = "/";

    public function __construct(?string $message = null, ?int $code = null, ?string $url = null)
    {
        $message = $message ?? $this->errorMessage;
        $code    = $code ?? $this->errorCode;
        $this->url = $url ?? $this->url;

        parent::__construct($message, $code);
    }
    public function getUrl(): string
    {
        return $this->url;
    }
}
