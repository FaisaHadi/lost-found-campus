<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ReviewReportRequest;
use App\Http\Resources\Api\V1\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportModerationController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function approve(Report $report): JsonResponse
    {
        $this->authorize('approve', $report);

        $report = $this->reportService->approve($report);

        return $this->successResponse(
            ReportResource::make($report),
            'Laporan berhasil disetujui.'
        );
    }

    public function reject(ReviewReportRequest $request, Report $report): JsonResponse
    {
        $this->authorize('reject', $report);

        $report = $this->reportService->reject($report, $request->validated('reason'));

        return $this->successResponse(
            ReportResource::make($report),
            'Laporan berhasil ditolak.'
        );
    }
}
