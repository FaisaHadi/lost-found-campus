<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ClaimResource;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimModerationController extends Controller
{
    public function __construct(
        private readonly ClaimService $claimService
    ) {}

    public function approve(Request $request, Claim $claim): JsonResponse
    {
        $this->authorize('approve', $claim);

        $claim = $this->claimService->approve($claim, $request->user());

        return $this->successResponse(
            ClaimResource::make($claim),
            'Klaim berhasil disetujui.'
        );
    }

    public function reject(Request $request, Claim $claim): JsonResponse
    {
        $this->authorize('reject', $claim);

        $claim = $this->claimService->reject($claim, $request->user());

        return $this->successResponse(
            ClaimResource::make($claim),
            'Klaim berhasil ditolak.'
        );
    }
}
