<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_id' => $this->report_id,
            'claimant_id' => $this->claimant_id,
            'proof_text' => $this->proof_text,
            'status' => $this->status,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image): array => [
                'id' => $image->id,
                'image_path' => $image->image_path,
                'image_url' => $image->image_url,
                'original_name' => $image->original_name,
                'mime_type' => $image->mime_type,
                'size' => $image->size,
                'sort_order' => $image->sort_order,
            ])->values()),
            'report' => ReportResource::make($this->whenLoaded('report')),
            'claimant' => UserResource::make($this->whenLoaded('claimant')),
            'reviewer' => UserResource::make($this->whenLoaded('reviewer')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
