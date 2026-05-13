<?php

namespace App\Repositories;

use App\Models\Claim;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClaimRepository
{
    public function paginateForUser(User $user, array $filters): LengthAwarePaginator
    {
        $query = Claim::query()
            ->with(['report.category', 'report.user', 'claimant', 'reviewer']);

        if (! $user->isAdmin()) {
            $query->where(function (Builder $query) use ($user) {
                $query->where('claimant_id', $user->id)
                    ->orWhereHas('report', fn (Builder $query) => $query->where('user_id', $user->id));
            });
        }

        $query->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
        $query->when($filters['report_id'] ?? null, fn (Builder $query, int $reportId) => $query->where('report_id', $reportId));

        if ($user->isAdmin()) {
            $query->when($filters['claimant_id'] ?? null, fn (Builder $query, int $claimantId) => $query->where('claimant_id', $claimantId));
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Claim
    {
        return Claim::query()
            ->with(['report.category', 'report.user', 'claimant', 'reviewer'])
            ->find($id);
    }

    public function create(array $data): Claim
    {
        return Claim::create($data)->load(['report.category', 'report.user', 'claimant', 'reviewer']);
    }

    public function update(Claim $claim, array $data): Claim
    {
        $claim->update($data);

        return $claim->refresh()->load(['report.category', 'report.user', 'claimant', 'reviewer']);
    }

    public function pendingClaimsForReportExcept(int $reportId, int $claimId)
    {
        return Claim::query()
            ->with(['claimant', 'report'])
            ->where('report_id', $reportId)
            ->whereKeyNot($claimId)
            ->where('status', 'pending')
            ->get();
    }

    public function existsForReportAndClaimant(int $reportId, int $claimantId): bool
    {
        return Claim::query()
            ->where('report_id', $reportId)
            ->where('claimant_id', $claimantId)
            ->exists();
    }
}
