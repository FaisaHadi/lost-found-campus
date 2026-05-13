<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ReportRepository
{
    public function paginateForUser(User $user, array $filters): LengthAwarePaginator
    {
        $query = Report::query()
            ->with(['user', 'category'])
            ->withCount('claims');

        if (! $user->isAdmin()) {
            $query->where(function (Builder $query) use ($user) {
                $query->where('moderation_status', 'approved')
                    ->orWhere('user_id', $user->id);
            });
        }

        $query->when($filters['keyword'] ?? null, function (Builder $query, string $keyword) {
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('location_text', 'like', "%{$keyword}%");
            });
        });

        $query->when($filters['category_id'] ?? null, fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId));
        $query->when($filters['category_slug'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('category', fn (Builder $query) => $query->where('slug', $slug)));
        $query->when($filters['report_type'] ?? null, fn (Builder $query, string $type) => $query->where('report_type', $type));
        $query->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        if ($user->isAdmin()) {
            $query->when($filters['moderation_status'] ?? null, fn (Builder $query, string $status) => $query->where('moderation_status', $status));
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_dir'] ?? 'desc';
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Report
    {
        return Report::query()
            ->with(['user', 'category'])
            ->withCount('claims')
            ->find($id);
    }

    public function create(array $data): Report
    {
        return Report::create($data)->load(['user', 'category'])->loadCount('claims');
    }

    public function update(Report $report, array $data): Report
    {
        $report->update($data);

        return $report->refresh()->load(['user', 'category'])->loadCount('claims');
    }

    public function delete(Report $report): bool
    {
        return (bool) $report->delete();
    }
}
