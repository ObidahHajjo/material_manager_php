<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    private string $errorMessage = "Validation failed.";
    private int $errorCode = 422;
    private string $url = "/";
    protected array $errors = [];

    public function __construct(?string $message, ?array $errors, ?int $code, ?string $url)
    {
        $message = $message ?? $this->errorMessage;
        $code    = $code ?? $this->errorCode;
        $this->url = $url ?? $this->url;

        parent::__construct($message, $code);
        $this->errors = $errors ?: [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
