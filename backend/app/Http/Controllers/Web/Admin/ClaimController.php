<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ClaimFilterRequest;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function __construct(
        private readonly ClaimService $claimService
    ) {}

    public function index(ClaimFilterRequest $request): View
    {
        $this->authorize('viewAny', Claim::class);

        return view('admin.claims.index', [
            'claims' => $this->claimService->listForUser($request->user(), $request->validated()),
            'filters' => $request->validated(),
        ]);
    }

    public function show(Claim $claim): View
    {
        $claim->load(['report.category', 'report.user', 'claimant', 'reviewer']);

        $this->authorize('view', $claim);

        return view('admin.claims.show', [
            'claim' => $claim,
        ]);
    }

    public function approve(Claim $claim): RedirectResponse
    {
        $this->authorize('approve', $claim);

        $this->claimService->approve($claim, request()->user());

        return back()->with('success', 'Klaim disetujui, status laporan diperbarui, dan pengaju diberi notifikasi.');
    }

    public function reject(Claim $claim): RedirectResponse
    {
        $this->authorize('reject', $claim);

        $this->claimService->reject($claim, request()->user());

        return back()->with('success', 'Klaim ditolak dan pengaju diberi notifikasi.');
    }
}
