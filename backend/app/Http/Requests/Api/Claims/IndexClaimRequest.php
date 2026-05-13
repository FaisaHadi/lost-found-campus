<?php

namespace App\Http\Requests\Api\Claims;

use App\Enums\ClaimStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexClaimRequest extends FormRequest
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
            'status' => ['sometimes', Rule::in(ClaimStatus::values())],
            'report_id' => ['sometimes', 'integer', 'exists:reports,id'],
            'claimant_id' => ['sometimes', 'integer', 'exists:users,id'],
            'sort_by' => ['sometimes', Rule::in(['created_at', 'updated_at', 'status'])],
            'sort_dir' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
