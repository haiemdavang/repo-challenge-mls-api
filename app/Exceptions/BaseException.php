<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected string $errorCode = '500';
    protected int $statusCode = 500;
    protected array $details = [];

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    public function getDetails(): array
    {
        return $this->details;
    }
}
