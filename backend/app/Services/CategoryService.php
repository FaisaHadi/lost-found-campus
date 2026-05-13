<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($filters);
    }

    public function create(array $data): Category
    {
        $data['slug'] = $this->uniqueSlug(filled($data['slug'] ?? null) ? $data['slug'] : $data['name']);

        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['name']) || isset($data['slug'])) {
            $data['slug'] = $this->uniqueSlug(
                filled($data['slug'] ?? null) ? $data['slug'] : ($data['name'] ?? $category->name),
                $category->id
            );
        }

        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->categoryRepository->slugExists($slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
