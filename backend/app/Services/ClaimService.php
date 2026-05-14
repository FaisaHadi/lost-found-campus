<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use App\Repositories\ClaimRepository;
use App\Repositories\ReportRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimService
{
    public function __construct(
        private readonly ClaimRepository $claimRepository,
        private readonly ReportRepository $reportRepository,
        private readonly NotificationService $notificationService,
        private readonly ImageStorageService $imageStorageService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @param array<string,mixed> $filters
     */
    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->claimRepository->paginateForUser($user, $filters);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(User $user, array $data): Claim
    {
        $report = $this->reportRepository->findById((int) $data['report_id']);

        if (! $report) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan tidak ditemukan.'],
            ]);
        }

        $this->ensureReportCanBeClaimed($report, $user);

        $imageFiles = $this->normalizeImages($data);
        $storedImages = [];

        try {
            foreach ($imageFiles as $image) {
                $storedImages[] = [
                    'path' => $this->imageStorageService->storeClaimImage($image),
                    'file' => $image,
                ];
            }

            return DB::transaction(function () use ($user, $data, $report, $storedImages): Claim {
                $claim = $this->claimRepository->create(array_merge(
                    Arr::except($data, ['images']),
                    [
                        'claimant_id' => $user->id,
                        'status' => ClaimStatus::Pending->value,
                    ]
                ));

                $this->persistClaimImages($claim, $storedImages);

                $this->notificationService->createForUser(
                    $report->user_id,
                    'Klaim baru masuk.',
                    'Ada pengguna yang mengajukan klaim pada laporan Anda.',
                    $report,
                    $claim
                );

                $this->activityLogService->record('create_claim', 'Pengguna mengajukan klaim barang.', $claim, $user);

                return $claim->refresh()->load(['report.category', 'report.user', 'claimant', 'reviewer', 'images']);
            });
        } catch (\Throwable $exception) {
            foreach ($storedImages as $storedImage) {
                $this->imageStorageService->delete($storedImage['path']);
            }

            throw $exception;
        }
    }

    public function approve(Claim $claim, User $reviewer): Claim
    {
        $this->ensureClaimIsPending($claim);

        return DB::transaction(function () use ($claim, $reviewer): Claim {
            $claim = $this->claimRepository->update($claim, [
                'status' => ClaimStatus::Approved->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $report = $claim->report;
            $this->reportRepository->update($report, [
                'status' => ReportStatus::Claimed->value,
            ]);

            $this->rejectCompetingClaims($claim, $reviewer);

            $this->notificationService->createForUser(
                $claim->claimant_id,
                'Klaim disetujui.',
                'Klaim Anda telah disetujui oleh admin.',
                $report,
                $claim
            );

            $this->activityLogService->record('approve_claim', 'Admin menyetujui klaim barang.', $claim, $reviewer);

            return $claim->refresh()->load(['report.category', 'report.user', 'claimant', 'reviewer', 'images']);
        });
    }

    public function reject(Claim $claim, User $reviewer, ?string $reason = null): Claim
    {
        $this->ensureClaimIsPending($claim);

        return DB::transaction(function () use ($claim, $reviewer, $reason): Claim {
            $claim = $this->claimRepository->update($claim, [
                'status' => ClaimStatus::Rejected->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->notificationService->createForUser(
                $claim->claimant_id,
                'Klaim ditolak.',
                $reason ?: 'Klaim Anda ditolak oleh admin.',
                $claim->report,
                $claim
            );

            $this->activityLogService->record('reject_claim', $reason ?: 'Admin menolak klaim barang.', $claim, $reviewer);

            return $claim->refresh()->load(['report.category', 'report.user', 'claimant', 'reviewer', 'images']);
        });
    }

    private function ensureReportCanBeClaimed(Report $report, User $user): void
    {
        if ((int) $report->user_id === (int) $user->id) {
            throw ValidationException::withMessages([
                'report_id' => ['Pemilik laporan tidak dapat mengklaim laporannya sendiri.'],
            ]);
        }

        if ($report->moderation_status !== ModerationStatus::Approved) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan harus diverifikasi admin sebelum dapat diklaim.'],
            ]);
        }

        if (! in_array($report->status, [ReportStatus::Pending, ReportStatus::Approved], true)) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan ini tidak tersedia untuk diklaim.'],
            ]);
        }

        $hasPendingClaim = $report->claims()
            ->where('claimant_id', $user->id)
            ->where('status', ClaimStatus::Pending->value)
            ->exists();

        if ($hasPendingClaim) {
            throw ValidationException::withMessages([
                'report_id' => ['Anda sudah memiliki klaim yang sedang ditinjau untuk laporan ini.'],
            ]);
        }
    }

    private function ensureClaimIsPending(Claim $claim): void
    {
        if ($claim->status !== ClaimStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => ['Klaim ini sudah diproses.'],
            ]);
        }
    }

    private function rejectCompetingClaims(Claim $approvedClaim, User $reviewer): void
    {
        $pendingClaims = Claim::query()
            ->where('report_id', $approvedClaim->report_id)
            ->whereKeyNot($approvedClaim->id)
            ->where('status', ClaimStatus::Pending->value)
            ->get();

        foreach ($pendingClaims as $claim) {
            $claim->update([
                'status' => ClaimStatus::Rejected->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->notificationService->createForUser(
                $claim->claimant_id,
                'Klaim ditolak.',
                'Klaim Anda ditolak karena klaim lain telah disetujui.',
                $claim->report,
                $claim
            );
        }
    }

    /**
     * @param array<string,mixed> $data
     * @return list<UploadedFile>
     */
    private function normalizeImages(array $data): array
    {
        $images = [];

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
    private function persistClaimImages(Claim $claim, array $storedImages): void
    {
        foreach ($storedImages as $index => $storedImage) {
            /** @var UploadedFile $file */
            $file = $storedImage['file'];

            $claim->images()->create([
                'image_path' => $storedImage['path'],
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sort_order' => $index,
            ]);
        }
    }
}
