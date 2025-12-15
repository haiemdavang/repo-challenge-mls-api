<?php

namespace App\DTOs\Commons;

class ItemError
{
    public string $field;
    public string $message;

    public function __construct(string $field, string $message)
    {
        $this->field = $field;
        $this->message = $message;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'message' => $this->message,
        ];
    }
}
