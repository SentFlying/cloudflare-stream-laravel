<?php

namespace SentFlying\CloudflareStreamLaravel\Exceptions;

use Exception;

class CloudflareStreamApiException extends Exception
{
    /**
     * The error details from the Cloudflare API.
     */
    protected array $errors = [];

    /**
     * Create a new Cloudflare Stream API exception instance.
     */
    public function __construct(string $message = "", int $code = 0, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Get the error details from the Cloudflare API.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
