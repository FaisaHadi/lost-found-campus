<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Categories\IndexCategoryRequest;
use App\Http\Requests\Api\Categories\StoreCategoryRequest;
use App\Http\Requests\Api\Categories\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function index(IndexCategoryRequest $request): JsonResponse
    {
        $categories = $this->categoryService->list($request->validated());

        return $this->paginatedResponse(
            CategoryResource::collection($categories),
            'Kategori berhasil diambil.'
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return $this->successResponse(
            CategoryResource::make($category),
            'Kategori berhasil dibuat.',
            201
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        return $this->successResponse(
            CategoryResource::make($category),
            'Kategori berhasil diperbarui.'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return $this->successResponse([], 'Kategori berhasil dihapus.');
    }
}
