<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ClaimFilterRequest;
use App\Models\Claim;
use App\Models\User;
use App\Services\ClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function __construct(
        private readonly ClaimService $claimService
    ) {}

    /**
     * Web admin login memakai guard web/session, bukan guard api/JWT.
     * Helper ini mencegah reviewer/admin terkirim null ke ClaimService.
     */
    private function currentAdmin(): User
    {
        $admin = Auth::guard('web')->user() ?? request()->user();

        abort_if($admin === null, 403, 'Admin user tidak ditemukan. Silakan login ulang.');

        return $admin;
    }

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

        $this->claimService->approve($claim, $this->currentAdmin());

        return back()->with('success', 'Klaim disetujui, status laporan diperbarui, dan pengaju diberi notifikasi.');
    }

    public function reject(Claim $claim): RedirectResponse
    {
        $this->authorize('reject', $claim);

        $reason = request()->input('reason')
            ?? request()->input('admin_note')
            ?? request()->input('rejection_reason')
            ?? request()->input('note')
            ?? 'Klaim ditolak oleh admin.';

        $this->claimService->reject($claim, $this->currentAdmin(), $reason);

        return back()->with('success', 'Klaim ditolak dan pengaju telah diberi notifikasi.');
    }
}
