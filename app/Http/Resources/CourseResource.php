<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public static $wrap = null;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullname' => $this->fullname,
            'shortname' => $this->shortname,
            'summary' => $this->summary,
            'image_url' => $this->image_url,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'format' => $this->format,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'teachers' => BaseUserResource::collection($this->whenLoaded('teachers')),
        ];
    }
}
