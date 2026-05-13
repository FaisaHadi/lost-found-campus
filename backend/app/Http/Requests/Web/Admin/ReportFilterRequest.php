<?php

namespace App\Http\Requests\Web\Admin;

use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['sometimes', 'nullable', 'string', 'max:100'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'category_slug' => ['sometimes', 'nullable', 'string', 'exists:categories,slug'],
            'report_type' => ['sometimes', 'nullable', Rule::in(ReportType::values())],
            'status' => ['sometimes', 'nullable', Rule::in(ReportStatus::values())],
            'moderation_status' => ['sometimes', 'nullable', Rule::in(ModerationStatus::values())],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'title', 'status', 'report_type'])],
            'sort_dir' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
