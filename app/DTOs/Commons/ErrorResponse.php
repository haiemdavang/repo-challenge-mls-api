<?php

namespace App\DTOs\Commons;

use Illuminate\Support\Str;
use Carbon\Carbon;

class ErrorResponse
{
    public string $code;
    public string $message;
    public array $details;

    public function __construct(
        string $message = "Lỗi hệ thống không xác định.",
        string $code = "500",
        array $details = []
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->details = $details;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'details' => array_map(function (ItemError $item) {
                return $item->toArray();
            }, $this->details),
        ];
    }
}
