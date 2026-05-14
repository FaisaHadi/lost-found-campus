<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Devices\StoreDeviceRequest;
use App\Models\UserDevice;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $device = UserDevice::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'device_id' => $data['device_id'] ?? null,
                'platform' => $data['platform'],
            ],
            [
                'push_token' => $data['push_token'] ?? null,
                'status' => $data['status'] ?? 'active',
                'last_used_at' => now(),
            ]
        );

        return $this->successResponse([
            'device' => $device,
        ], 'Token perangkat berhasil disimpan.');
    }
}
