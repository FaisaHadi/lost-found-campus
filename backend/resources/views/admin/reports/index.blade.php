@extends('layouts.admin')

@section('title', 'Report Management')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filters" subtitle="Search and segment reports by moderation criteria">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label for="keyword" class="admin-label">Search</label>
                    <input id="keyword" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="admin-control mt-1" placeholder="Title, description, location">
                </div>
                <div>
                    <label for="category_id" class="admin-label">Category</label>
                    <select id="category_id" name="category_id" class="admin-control mt-1">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report_type" class="admin-label">Type</label>
                    <select id="report_type" name="report_type" class="admin-control mt-1">
                        <option value="">All types</option>
                        <option value="lost" @selected(($filters['report_type'] ?? '') === 'lost')>Lost</option>
                        <option value="found" @selected(($filters['report_type'] ?? '') === 'found')>Found</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'approved', 'rejected', 'claimed', 'completed'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str($status)->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Apply</x-ui.button>
                    <x-ui.button :href="route('admin.reports.index')" variant="secondary">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Reports" subtitle="Review submissions, inspect details, and moderate lifecycle state">
            @if ($reports->isEmpty())
                <x-ui.empty-state title="No reports found" message="Try changing filters or wait for new submissions." />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Report</th>
                                <th class="admin-table-heading">Reporter</th>
                                <th class="admin-table-heading">Type</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Moderation</th>
                                <th class="admin-table-heading">Submitted</th>
                                <th class="admin-table-heading text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="admin-table-cell">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="font-medium text-gray-950 hover:text-emerald-700">{{ $report->title }}</a>
                                        <p class="mt-1 text-xs text-gray-500">{{ $report->category?->name ?? 'Uncategorized' }} · {{ $report->location_text ?: 'No location text' }}</p>
                                    </td>
                                    <td class="admin-table-cell">{{ $report->user?->name }}</td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->report_type?->value ?? $report->report_type" /></td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->status?->value ?? $report->status" /></td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->moderation_status?->value ?? $report->moderation_status" /></td>
                                    <td class="admin-table-cell text-gray-500">{{ $report->created_at->format('M j, Y') }}</td>
                                    <td class="admin-table-cell">
                                        <div class="flex justify-end gap-2">
                                            <x-ui.button :href="route('admin.reports.show', $report)" variant="secondary" size="sm">Open</x-ui.button>
                                            @if (($report->moderation_status?->value ?? $report->moderation_status) === 'pending')
                                                <form method="POST" action="{{ route('admin.reports.approve', $report) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button size="sm" data-loading-button data-loading-text="Approving...">Approve</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.reject', $report) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button variant="danger" size="sm" data-loading-button data-loading-text="Rejecting...">Reject</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $reports->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
