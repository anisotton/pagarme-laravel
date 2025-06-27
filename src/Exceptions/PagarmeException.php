<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Exceptions;

use Exception;

class PagarmeException extends Exception
{
    protected array $errors = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
        array $errors = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    public static function invalidApiKey(): self
    {
        return new self('Invalid API key provided', 401);
    }

    public static function invalidRequest(string $message, array $errors = []): self
    {
        return new self($message, 400, null, $errors);
    }

    public static function authenticationFailed(): self
    {
        return new self('Authentication failed', 401);
    }

    public static function resourceNotFound(string $resource = 'Resource'): self
    {
        return new self("{$resource} not found", 404);
    }

    public static function rateLimitExceeded(): self
    {
        return new self('Rate limit exceeded', 429);
    }

    public static function serverError(string $message = 'Internal server error'): self
    {
        return new self($message, 500);
    }
}
