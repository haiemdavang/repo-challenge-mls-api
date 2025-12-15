<?php

namespace App\Exceptions;

class BadRequestException extends BaseException
{
    protected string $errorCode = '400';
    protected int $statusCode = 400;

    public function __construct(string $message = 'BAD_REQUEST', array $details = [])
    {
        parent::__construct($message);
        $this->details = $details;
    }
}
