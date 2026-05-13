<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CategoryRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Category::query();

        $query->when($filters['keyword'] ?? null, function (Builder $query, string $keyword) {
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        });

        $query->when(
            array_key_exists('status', $filters) && filled($filters['status']),
            fn (Builder $query) => $query->where('status', $filters['status']),
            fn (Builder $query) => ($filters['all_statuses'] ?? false)
                ? $query
                : $query->where('status', 'active')
        );

        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_dir'] ?? 'asc';
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }

    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Category::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
