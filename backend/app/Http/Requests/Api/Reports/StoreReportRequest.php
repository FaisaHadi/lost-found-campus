<?php

namespace App\Http\Requests\Api\Reports;

use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'report_type' => ['required', new Enum(ReportType::class)],
            'image' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:4096'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
