<?php

namespace App\Http\Requests\Api\Reports;

use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexReportRequest extends FormRequest
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
            'keyword' => ['sometimes', 'string', 'max:100'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'category_slug' => ['sometimes', 'string', 'exists:categories,slug'],
            'report_type' => ['sometimes', Rule::in(ReportType::values())],
            'status' => ['sometimes', Rule::in(ReportStatus::values())],
            'moderation_status' => ['sometimes', Rule::in(ModerationStatus::values())],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'title', 'status', 'report_type'])],
            'sort_dir' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
