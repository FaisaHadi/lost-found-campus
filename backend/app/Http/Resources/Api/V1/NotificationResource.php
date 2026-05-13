<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status?->value ?? $this->status,
            'read_at' => $this->read_at?->toISOString(),
            'report_id' => $this->report_id,
            'claim_id' => $this->claim_id,
            'report' => ReportResource::make($this->whenLoaded('report')),
            'claim' => ClaimResource::make($this->whenLoaded('claim')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
