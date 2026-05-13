<?php

namespace App\Http\Requests\Api\Claims;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
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
            'report_id' => ['required', 'integer', 'exists:reports,id'],
            'proof_text' => ['required', 'string', 'min:20', 'max:5000'],
        ];
    }
}
