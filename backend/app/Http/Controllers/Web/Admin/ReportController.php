<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\ReportFilterRequest;
use App\Http\Requests\Web\Admin\ReportUpdateRequest;
use App\Models\Category;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

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
        $reason = $validated['reason'] ?? null;

        $updateData = Arr::except($validated, ['status', 'reason']);

        if ($updateData !== []) {
            $report = $this->reportService->update($report, $updateData);
        }

        if ($status) {
            $this->reportService->changeStatus($report->refresh(), $status, $reason);
        }

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function approve(Report $report): RedirectResponse
    {
        $this->authorize('approve', $report);

        $this->reportService->approve($report);

        return back()->with('success', 'Laporan disetujui dan pemilik telah diberi notifikasi.');
    }

    public function reject(ReportUpdateRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('reject', $report);

        $this->reportService->reject($report, $request->validated('reason'));

        return back()->with('success', 'Laporan ditolak dan pemilik telah diberi notifikasi.');
    }
}
