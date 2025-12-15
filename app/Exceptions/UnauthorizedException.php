<?php

namespace App\Exceptions;

class UnauthorizedException extends BaseException
{
    protected string $errorCode = '401';
    protected int $statusCode = 401;

    public function __construct(string $message = 'UNAUTHORIZED')
    {
        parent::__construct($message);
    }
}
