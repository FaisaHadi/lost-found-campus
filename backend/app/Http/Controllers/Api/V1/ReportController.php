<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Reports\IndexReportRequest;
use App\Http\Requests\Api\Reports\StoreReportRequest;
use App\Http\Requests\Api\Reports\UpdateReportRequest;
use App\Http\Resources\Api\V1\ReportResource;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function index(IndexReportRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Report::class);

        $reports = $this->reportService->listForUser($request->user(), $request->validated());

        return $this->paginatedResponse(
            ReportResource::collection($reports),
            'Reports retrieved successfully.'
        );
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        $this->authorize('create', Report::class);

        $report = $this->reportService->create($request->user(), $request->validated());

        return $this->successResponse(
            ReportResource::make($report),
            'Report created successfully and is pending moderation.',
            201
        );
    }

    public function show(Report $report): JsonResponse
    {
        $report->load(['user', 'category'])->loadCount('claims');

        $this->authorize('view', $report);

        return $this->successResponse(
            ReportResource::make($report),
            'Report retrieved successfully.'
        );
    }

    public function update(UpdateReportRequest $request, Report $report): JsonResponse
    {
        $this->authorize('update', $report);

        $report = $this->reportService->update($report, $request->validated());

        return $this->successResponse(
            ReportResource::make($report),
            'Report updated successfully.'
        );
    }

    public function destroy(Report $report): JsonResponse
    {
        $this->authorize('delete', $report);

        $this->reportService->delete($report);

        return $this->successResponse([], 'Report deleted successfully.');
    }
}
