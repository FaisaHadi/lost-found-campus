<?php

namespace App\Repositories;

use App\Models\Claim;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClaimRepository
{
    /**
     * @param array<string,mixed> $filters
     */
    public function paginateForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Claim::query()
            ->with(['report.category', 'report.user', 'claimant', 'reviewer', 'images'])
            ->latest();

        if (! $user->isAdmin()) {
            $query->where(function (Builder $builder) use ($user): void {
                $builder->where('claimant_id', $user->id)
                    ->orWhereHas('report', function (Builder $reportQuery) use ($user): void {
                        $reportQuery->where('user_id', $user->id);
                    });
            });
        }

        foreach (['status', 'report_id'] as $filter) {
            if (! empty($filters[$filter])) {
                $query->where($filter, $filters[$filter]);
            }
        }

        return $query->paginate((int) ($filters['per_page'] ?? 10));
    }

    public function findById(int $id): ?Claim
    {
        return Claim::query()
            ->with(['report.category', 'report.user', 'claimant', 'reviewer', 'images'])
            ->find($id);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): Claim
    {
        return Claim::query()->create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(Claim $claim, array $data): Claim
    {
        $claim->update($data);

        return $claim;
    }
}
