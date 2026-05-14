<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ReportRepository
{
    /**
     * @param array<string,mixed> $filters
     */
    public function paginateForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Report::query()
            ->with(['user', 'category', 'images'])
            ->withCount('claims')
            ->latest();

        if (! $user->isAdmin()) {
            $query->where(function (Builder $builder) use ($user): void {
                $builder->where('user_id', $user->id)
                    ->orWhere('moderation_status', 'approved');
            });
        }

        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        foreach (['report_type', 'status', 'moderation_status', 'category_id'] as $filter) {
            if (! empty($filters[$filter])) {
                $query->where($filter, $filters[$filter]);
            }
        }

        return $query->paginate((int) ($filters['per_page'] ?? 10));
    }

    public function findById(int $id): ?Report
    {
        return Report::query()
            ->with(['user', 'category', 'images', 'claims.claimant'])
            ->withCount('claims')
            ->find($id);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): Report
    {
        return Report::query()->create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(Report $report, array $data): Report
    {
        $report->update($data);

        return $report;
    }

    public function delete(Report $report): void
    {
        $report->delete();
    }
}
