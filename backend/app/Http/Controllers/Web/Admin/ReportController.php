<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ReportFilterRequest;
use App\Http\Requests\Web\Admin\ReportUpdateRequest;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Web admin login memakai guard web/session, bukan guard api/JWT.
     * Helper ini mencegah $admin terkirim null ke ReportService.
     */
    private function currentAdmin(): User
    {
        $admin = Auth::guard('web')->user() ?? request()->user();

        abort_if($admin === null, 403, 'Admin user tidak ditemukan. Silakan login ulang.');

        return $admin;
    }

    public function index(ReportFilterRequest $request): View
    {
        $this->authorize('viewAny', Report::class);

        return view('admin.reports.index', [
            'reports' => $this->reportService->listForUser($request->user(), $request->validated()),
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->validated(),
        ]);
    }

    public function show(Report $report): View
    {
        $report->load(['user', 'category', 'claims.claimant', 'claims.reviewer'])->loadCount('claims');

        $this->authorize('view', $report);

        return view('admin.reports.show', [
            'report' => $report,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => ReportStatus::values(),
        ]);
    }

    public function update(ReportUpdateRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        $validated = $request->validated();
        $status = $validated['status'] ?? null;
        $updateData = Arr::except($validated, ['status', 'reason']);

        if ($updateData !== []) {
            $report = $this->reportService->update($report, $updateData);
        }

        if ($status) {
            $statusEnum = $status instanceof ReportStatus ? $status : ReportStatus::from($status);
            $this->reportService->changeStatus($report->refresh(), $statusEnum);
        }

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function approve(Report $report): RedirectResponse
    {
        $this->authorize('approve', $report);

        $this->reportService->approve($report, $this->currentAdmin());

        return back()->with('success', 'Laporan disetujui dan pemilik telah diberi notifikasi.');
    }

    public function reject(ReportUpdateRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('reject', $report);

        $validated = $request->validated();

        $reason = $validated['reason']
            ?? $request->input('reason')
            ?? $request->input('admin_note')
            ?? $request->input('rejection_reason')
            ?? $request->input('note')
            ?? 'Laporan ditolak oleh admin.';

        $this->reportService->reject($report, $this->currentAdmin(), $reason);

        return back()->with('success', 'Laporan ditolak dan pemilik telah diberi notifikasi.');
    }
}
