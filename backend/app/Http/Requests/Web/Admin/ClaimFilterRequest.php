<?php

namespace App\Http\Requests\Web\Admin;

use App\Enums\ClaimStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClaimFilterRequest extends FormRequest
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
            'status' => ['sometimes', 'nullable', Rule::in(ClaimStatus::values())],
            'report_id' => ['sometimes', 'nullable', 'integer', 'exists:reports,id'],
            'claimant_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'status'])],
            'sort_dir' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
