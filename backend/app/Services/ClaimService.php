<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\ModerationStatus;
use App\Enums\ReportStatus;
use App\Models\Claim;
use App\Models\User;
use App\Repositories\ClaimRepository;
use App\Repositories\ReportRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimService
{
    public function __construct(
        private readonly ClaimRepository $claimRepository,
        private readonly ReportRepository $reportRepository,
        private readonly NotificationService $notificationService
    ) {}

    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->claimRepository->paginateForUser($user, $filters);
    }

    public function find(int $id): ?Claim
    {
        return $this->claimRepository->findById($id);
    }

    public function create(User $user, array $data): Claim
    {
        $report = $this->reportRepository->findById((int) $data['report_id']);

        if (! $report) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan yang dipilih tidak ditemukan.'],
            ]);
        }

        if ($report->user_id === $user->id) {
            throw ValidationException::withMessages([
                'report_id' => ['Pengguna tidak dapat mengklaim laporan miliknya sendiri.'],
            ]);
        }

        if (
            $report->moderation_status !== ModerationStatus::Approved
            || $report->status !== ReportStatus::Approved
        ) {
            throw ValidationException::withMessages([
                'report_id' => ['Hanya laporan yang disetujui dan belum diklaim yang dapat diklaim.'],
            ]);
        }

        if ($this->claimRepository->existsForReportAndClaimant($report->id, $user->id)) {
            throw ValidationException::withMessages([
                'report_id' => ['Anda sudah mengajukan klaim untuk laporan ini.'],
            ]);
        }

        return $this->claimRepository->create([
            'report_id' => $report->id,
            'claimant_id' => $user->id,
            'proof_text' => $data['proof_text'],
            'status' => ClaimStatus::Pending->value,
        ]);
    }

    public function approve(Claim $claim, User $reviewer): Claim
    {
        $this->ensureClaimIsPending($claim);

        $claim = DB::transaction(function () use ($claim, $reviewer) {
            $claim = $this->claimRepository->update($claim, [
                'status' => ClaimStatus::Approved->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->reportRepository->update($claim->report, [
                'status' => ReportStatus::Claimed->value,
            ]);

            $this->rejectCompetingClaims($claim, $reviewer);

            return $claim->refresh()->load(['report.category', 'report.user', 'claimant', 'reviewer']);
        });

        $this->notificationService->createForUser(
            $claim->claimant_id,
            'Klaim disetujui',
            "Klaim Anda untuk \"{$claim->report->title}\" telah disetujui.",
            $claim->report,
            $claim
        );

        return $claim;
    }

    public function reject(Claim $claim, User $reviewer): Claim
    {
        $this->ensureClaimIsPending($claim);

        $claim = DB::transaction(function () use ($claim, $reviewer) {
            return $this->claimRepository->update($claim, [
                'status' => ClaimStatus::Rejected->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);
        });

        $this->notificationService->createForUser(
            $claim->claimant_id,
            'Klaim ditolak',
            "Klaim Anda untuk \"{$claim->report->title}\" telah ditolak.",
            $claim->report,
            $claim
        );

        return $claim;
    }

    private function rejectCompetingClaims(Claim $approvedClaim, User $reviewer): void
    {
        $claims = $this->claimRepository->pendingClaimsForReportExcept(
            $approvedClaim->report_id,
            $approvedClaim->id
        );

        foreach ($claims as $claim) {
            $claim = $this->claimRepository->update($claim, [
                'status' => ClaimStatus::Rejected->value,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->notificationService->createForUser(
                $claim->claimant_id,
                'Klaim ditolak',
                "Klaim Anda untuk \"{$claim->report->title}\" ditolak karena klaim lain telah disetujui.",
                $claim->report,
                $claim
            );
        }
    }

    private function ensureClaimIsPending(Claim $claim): void
    {
        if ($claim->status !== ClaimStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => ['Hanya klaim berstatus menunggu yang dapat ditinjau.'],
            ]);
        }
    }
}
