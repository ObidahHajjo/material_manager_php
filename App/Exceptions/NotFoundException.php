<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    private string $errorMessage = "The requested resource was not found.";
    private int $errorCode = 404;
    private string $url = "/";

    public function __construct(?string $message = null, ?int $code = null, ?string $url = null)
    {
        $message = $message ?? $this->errorMessage;
        $code    = $code ?? $this->errorCode;
        $this->url = $url ?? $this->url;

        parent::__construct($message, $code);
    }
}
