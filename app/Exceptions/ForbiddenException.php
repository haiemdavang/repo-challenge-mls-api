<?php

namespace App\Exceptions;

class ForbiddenException extends BaseException
{
    protected string $errorCode = '403';
    protected int $statusCode = 403;

    public function __construct(string $message = 'FORBIDDEN')
    {
        parent::__construct($message);
    }
}
