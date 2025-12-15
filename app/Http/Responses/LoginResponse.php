<?php

namespace App\Http\Responses;

class LoginResponse
{
    public function __construct(
        public string $accessToken,
        public array $user
    ) {}

    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'user' => $this->user,
        ];
    }
}
