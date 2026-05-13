<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\CategoryFilterRequest;
use App\Http\Requests\Web\Admin\CategorySaveRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function index(CategoryFilterRequest $request): View
    {
        return view('admin.categories.index', [
            'categories' => $this->categoryService->list([
                ...$request->validated(),
                'all_statuses' => true,
            ]),
            'filters' => $request->validated(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['status' => 'active']),
        ]);
    }

    public function store(CategorySaveRequest $request): RedirectResponse
    {
        $category = $this->categoryService->create($request->validated());

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(CategorySaveRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
