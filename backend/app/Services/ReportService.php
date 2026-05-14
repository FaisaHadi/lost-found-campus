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
        private readonly NotificationService $notificationService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @param array<string,mixed> $filters
     */
    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->reportRepository->paginateForUser($user, $filters);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(User $user, array $data): Report
    {
        $imageFiles = $this->normalizeImages($data);
        $storedImages = [];

        try {
            foreach ($imageFiles as $image) {
                $storedImages[] = [
                    'path' => $this->imageStorageService->storeReportImage($image),
                    'file' => $image,
                ];
            }

            return DB::transaction(function () use ($user, $data, $storedImages): Report {
                $report = $this->reportRepository->create(array_merge(
                    Arr::except($data, ['image', 'images', 'remove_image', 'status', 'moderation_status']),
                    [
                        'user_id' => $user->id,
                        'image_path' => $storedImages[0]['path'] ?? null,
                        'status' => ReportStatus::Pending->value,
                        'moderation_status' => ModerationStatus::Pending->value,
                    ]
                ));

                $this->persistReportImages($report, $storedImages);
                $this->activityLogService->record('create_report', 'Pengguna membuat laporan barang.', $report, $user);

                return $report->refresh()->load(['user', 'category', 'images'])->loadCount('claims');
            });
        } catch (\Throwable $exception) {
            foreach ($storedImages as $storedImage) {
                $this->imageStorageService->delete($storedImage['path']);
            }

            throw $exception;
        }
    }

    public function find(int $id): ?Report
    {
        return $this->reportRepository->findById($id);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(Report $report, array $data): Report
    {
        $imageFiles = $this->normalizeImages($data);
        $storedImages = [];

        try {
            foreach ($imageFiles as $image) {
                $storedImages[] = [
                    'path' => $this->imageStorageService->storeReportImage($image),
                    'file' => $image,
                ];
            }

            return DB::transaction(function () use ($report, $data, $storedImages): Report {
                if (($data['remove_image'] ?? false) === true) {
                    $this->deleteAllReportImages($report);
                    $data['image_path'] = null;
                } elseif ($storedImages !== []) {
                    $data['image_path'] = $storedImages[0]['path'];
                }

                $report = $this->reportRepository->update($report, Arr::except($data, ['image', 'images', 'remove_image']));
                $this->persistReportImages($report, $storedImages);
                $this->activityLogService->record('update_report', 'Pengguna memperbarui laporan barang.', $report);

                return $report->refresh()->load(['user', 'category', 'images'])->loadCount('claims');
            });
        } catch (\Throwable $exception) {
            foreach ($storedImages as $storedImage) {
                $this->imageStorageService->delete($storedImage['path']);
            }

            throw $exception;
        }
    }

    public function delete(Report $report): void
    {
        $imagePaths = $report->images()->pluck('image_path')->all();
        if ($report->image_path) {
            $imagePaths[] = $report->image_path;
        }

        DB::transaction(function () use ($report): void {
            $this->activityLogService->record('delete_report', 'Pengguna menghapus laporan barang.', $report);
            $this->reportRepository->delete($report);
        });

        foreach (array_unique(array_filter($imagePaths)) as $imagePath) {
            $this->imageStorageService->delete($imagePath);
        }
    }

    public function approve(Report $report, User $admin): Report
    {
        return $this->setModerationState(
            $report,
            ModerationStatus::Approved,
            $admin,
            'Laporan Anda telah diverifikasi.',
            'Laporan barang telah disetujui oleh admin.',
            'verify_report'
        );
    }

    public function reject(Report $report, User $admin, ?string $reason = null): Report
    {
        return $this->setModerationState(
            $report,
            ModerationStatus::Rejected,
            $admin,
            'Laporan Anda ditolak.',
            $reason ?: 'Laporan barang ditolak oleh admin.',
            'reject_report'
        );
    }

    public function changeStatus(Report $report, ReportStatus $status): Report
    {
        return $this->reportRepository->update($report, [
            'status' => $status->value,
        ]);
    }

    public function setClaimed(Report $report): Report
    {
        return $this->changeStatus($report, ReportStatus::Claimed);
    }

    public function setCompleted(Report $report): Report
    {
        $this->ensureReportCanBeCompleted($report);

        $updatedReport = $this->changeStatus($report, ReportStatus::Completed);
        $this->activityLogService->record('complete_report', 'Status laporan diubah menjadi selesai.', $updatedReport);

        return $updatedReport;
    }

    private function setModerationState(
        Report $report,
        ModerationStatus $status,
        User $admin,
        string $title,
        string $message,
        string $action
    ): Report {
        return DB::transaction(function () use ($report, $status, $admin, $title, $message, $action): Report {
            $updatedReport = $this->reportRepository->update($report, [
                'moderation_status' => $status->value,
                'moderated_by' => $admin->id,
                'moderated_at' => now(),
            ]);

            $this->notificationService->createForUser(
                $updatedReport->user_id,
                $title,
                $message,
                $updatedReport
            );

            $this->activityLogService->record($action, $message, $updatedReport, $admin);

            return $updatedReport->refresh()->load(['user', 'category', 'images'])->loadCount('claims');
        });
    }

    private function ensureReportCanBeCompleted(Report $report): void
    {
        if ($report->status !== ReportStatus::Claimed) {
            throw ValidationException::withMessages([
                'status' => ['Laporan hanya dapat diselesaikan setelah klaim disetujui.'],
            ]);
        }
    }

    /**
     * @param array<string,mixed> $data
     * @return list<UploadedFile>
     */
    private function normalizeImages(array $data): array
    {
        $images = [];

        if (($data['image'] ?? null) instanceof UploadedFile) {
            $images[] = $data['image'];
        }

        foreach (($data['images'] ?? []) as $image) {
            if ($image instanceof UploadedFile) {
                $images[] = $image;
            }
        }

        return $images;
    }

    /**
     * @param list<array{path:string,file:UploadedFile}> $storedImages
     */
    private function persistReportImages(Report $report, array $storedImages): void
    {
        foreach ($storedImages as $index => $storedImage) {
            /** @var UploadedFile $file */
            $file = $storedImage['file'];

            $report->images()->create([
                'image_path' => $storedImage['path'],
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sort_order' => $index,
            ]);
        }
    }

    private function deleteAllReportImages(Report $report): void
    {
        $report->loadMissing('images');

        foreach ($report->images as $image) {
            $this->imageStorageService->delete($image->image_path);
            $image->delete();
        }

        if ($report->image_path) {
            $this->imageStorageService->delete($report->image_path);
        }
    }
}
