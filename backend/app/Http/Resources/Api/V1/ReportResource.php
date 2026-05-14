<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReportResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'report_type' => $this->report_type,
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image): array => [
                'id' => $image->id,
                'image_path' => $image->image_path,
                'image_url' => $image->image_url,
                'original_name' => $image->original_name,
                'mime_type' => $image->mime_type,
                'size' => $image->size,
                'sort_order' => $image->sort_order,
            ])->values()),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_text' => $this->location_text,
            'status' => $this->status,
            'moderation_status' => $this->moderation_status,
            'moderated_at' => $this->moderated_at,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'claims_count' => $this->whenCounted('claims'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
