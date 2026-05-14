<?php

namespace App\Http\Requests\Api\Devices;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
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
            'device_id' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', 'string', 'in:web,android'],
            'push_token' => ['nullable', 'string', 'max:2048'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
