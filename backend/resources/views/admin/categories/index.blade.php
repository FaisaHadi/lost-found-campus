@extends('layouts.admin')

@section('title', 'Category Management')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.create')">Create category</x-ui.button>
@endsection

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filters" subtitle="Manage category availability for report filtering">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="keyword" class="admin-label">Search</label>
                    <input id="keyword" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="admin-control mt-1" placeholder="Name, slug, description">
                </div>
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">All statuses</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Apply</x-ui.button>
                    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Categories" subtitle="Reusable categories shared by web admin and mobile clients">
            @if ($categories->isEmpty())
                <x-ui.empty-state title="No categories found" message="Create the first category to start organizing reports.">
                    <x-ui.button :href="route('admin.categories.create')">Create category</x-ui.button>
                </x-ui.empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Name</th>
                                <th class="admin-table-heading">Slug</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Updated</th>
                                <th class="admin-table-heading text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="admin-table-cell">
                                        <p class="font-medium text-gray-950">{{ $category->name }}</p>
                                        <p class="mt-1 max-w-xl text-xs text-gray-500">{{ $category->description ?: 'No description' }}</p>
                                    </td>
                                    <td class="admin-table-cell text-gray-500">{{ $category->slug }}</td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$category->status" /></td>
                                    <td class="admin-table-cell text-gray-500">{{ $category->updated_at->format('M j, Y') }}</td>
                                    <td class="admin-table-cell">
                                        <div class="flex justify-end gap-2">
                                            <x-ui.button :href="route('admin.categories.edit', $category)" variant="secondary" size="sm">Edit</x-ui.button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-loading-form>
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" size="sm" data-loading-button data-loading-text="Deleting...">Delete</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $categories->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
