<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fullname' => $this->full_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'gender' => $this->gender,
            'id_number' => $this->id_number,
            'department' => $this->department,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
