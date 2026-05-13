<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Claims\IndexClaimRequest;
use App\Http\Requests\Api\Claims\StoreClaimRequest;
use App\Http\Resources\Api\V1\ClaimResource;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\JsonResponse;

class ClaimController extends Controller
{
    public function __construct(
        private readonly ClaimService $claimService
    ) {}

    public function index(IndexClaimRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Claim::class);

        $claims = $this->claimService->listForUser($request->user(), $request->validated());

        return $this->paginatedResponse(
            ClaimResource::collection($claims),
            'Klaim berhasil diambil.'
        );
    }

    public function store(StoreClaimRequest $request): JsonResponse
    {
        $this->authorize('create', Claim::class);

        $claim = $this->claimService->create($request->user(), $request->validated());

        return $this->successResponse(
            ClaimResource::make($claim),
            'Klaim berhasil dikirim dan menunggu tinjauan admin.',
            201
        );
    }

    public function show(Claim $claim): JsonResponse
    {
        $claim->load(['report.category', 'report.user', 'claimant', 'reviewer']);

        $this->authorize('view', $claim);

        return $this->successResponse(
            ClaimResource::make($claim),
            'Klaim berhasil diambil.'
        );
    }
}
