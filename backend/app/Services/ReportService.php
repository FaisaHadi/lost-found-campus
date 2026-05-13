<?php

namespace App\Services;

use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly ImageStorageService $imageStorageService,
        private readonly NotificationService $notificationService
    ) {}

    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->reportRepository->paginateForUser($user, $filters);
    }

    public function create(User $user, array $data): Report
    {
        $imagePath = null;

        if (($data['image'] ?? null) instanceof UploadedFile) {
            $imagePath = $this->imageStorageService->storeReportImage($data['image']);
        }

        try {
            return DB::transaction(function () use ($user, $data, $imagePath) {
                return $this->reportRepository->create([
                    ...Arr::except($data, ['image', 'remove_image', 'status']),
                    'user_id' => $user->id,
                    'image_path' => $imagePath,
                    'status' => ReportStatus::Pending->value,
                    'moderation_status' => ModerationStatus::Pending->value,
                ]);
            });
        } catch (\Throwable $exception) {
            $this->imageStorageService->delete($imagePath);

            throw $exception;
        }
    }

    public function find(int $id): ?Report
    {
        return $this->reportRepository->findById($id);
    }

    public function update(Report $report, array $data): Report
    {
        $oldImagePath = $report->image_path;
        $newImagePath = null;

        if (($data['image'] ?? null) instanceof UploadedFile) {
            $newImagePath = $this->imageStorageService->storeReportImage($data['image']);
            $data['image_path'] = $newImagePath;
        }

        if (($data['remove_image'] ?? false) && ! $newImagePath) {
            $data['image_path'] = null;
        }

        if (($data['status'] ?? null) === ReportStatus::Completed->value) {
            $this->ensureReportCanBeCompleted($report);
        }

        try {
            $updatedReport = DB::transaction(function () use ($report, $data) {
                return $this->reportRepository->update(
                    $report,
                    Arr::except($data, ['image', 'remove_image'])
                );
            });
        } catch (\Throwable $exception) {
            $this->imageStorageService->delete($newImagePath);

            throw $exception;
        }

        if ($newImagePath || array_key_exists('image_path', $data)) {
            $this->imageStorageService->delete($oldImagePath);
        }

        return $updatedReport;
    }

    public function delete(Report $report): void
    {
        DB::transaction(function () use ($report) {
            $this->reportRepository->delete($report);
        });

        $this->imageStorageService->delete($report->image_path);
    }

    public function approve(Report $report): Report
    {
        $report = DB::transaction(function () use ($report) {
            return $this->reportRepository->update($report, [
                'status' => ReportStatus::Approved->value,
                'moderation_status' => ModerationStatus::Approved->value,
            ]);
        });

        $this->notificationService->createForUser(
            $report->user_id,
            'Laporan disetujui',
            "Laporan Anda \"{$report->title}\" telah disetujui.",
            $report
        );

        return $report;
    }

    public function reject(Report $report, ?string $reason = null): Report
    {
        $report = DB::transaction(function () use ($report) {
            return $this->reportRepository->update($report, [
                'status' => ReportStatus::Rejected->value,
                'moderation_status' => ModerationStatus::Rejected->value,
            ]);
        });

        $message = "Laporan Anda \"{$report->title}\" telah ditolak.";

        if ($reason) {
            $message .= " Alasan: {$reason}";
        }

        $this->notificationService->createForUser(
            $report->user_id,
            'Laporan ditolak',
            $message,
            $report
        );

        return $report;
    }

    public function changeStatus(Report $report, string $status, ?string $reason = null): Report
    {
        if ($status === ($report->status?->value ?? $report->status)) {
            return $report->load(['user', 'category'])->loadCount('claims');
        }

        return match ($status) {
            ReportStatus::Pending->value => $this->setModerationState(
                $report,
                ReportStatus::Pending,
                ModerationStatus::Pending
            ),
            ReportStatus::Approved->value => $this->approve($report),
            ReportStatus::Rejected->value => $this->reject($report, $reason),
            ReportStatus::Claimed->value => $this->setClaimed($report),
            ReportStatus::Completed->value => $this->setCompleted($report),
            default => throw ValidationException::withMessages([
                'status' => ['Status laporan yang dipilih tidak valid.'],
            ]),
        };
    }

    private function setModerationState(
        Report $report,
        ReportStatus $status,
        ModerationStatus $moderationStatus
    ): Report {
        return DB::transaction(function () use ($report, $status, $moderationStatus) {
            return $this->reportRepository->update($report, [
                'status' => $status->value,
                'moderation_status' => $moderationStatus->value,
            ]);
        });
    }

    private function setClaimed(Report $report): Report
    {
        if ($report->moderation_status !== ModerationStatus::Approved) {
            throw ValidationException::withMessages([
                'status' => ['Hanya laporan yang sudah disetujui yang dapat ditandai sebagai diklaim.'],
            ]);
        }

        return DB::transaction(function () use ($report) {
            return $this->reportRepository->update($report, [
                'status' => ReportStatus::Claimed->value,
            ]);
        });
    }

    private function setCompleted(Report $report): Report
    {
        $this->ensureReportCanBeCompleted($report);

        return DB::transaction(function () use ($report) {
            return $this->reportRepository->update($report, [
                'status' => ReportStatus::Completed->value,
            ]);
        });
    }

    private function ensureReportCanBeCompleted(Report $report): void
    {
        if ($report->status !== ReportStatus::Claimed) {
            throw ValidationException::withMessages([
                'status' => ['Hanya laporan yang sudah diklaim yang dapat ditandai sebagai selesai.'],
            ]);
        }
    }
}
