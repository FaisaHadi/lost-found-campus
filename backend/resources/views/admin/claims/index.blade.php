@extends('layouts.admin')

@section('title', 'Claim Management')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filters" subtitle="Review claims by status, report, or claimant">
            <form method="GET" action="{{ route('admin.claims.index') }}" class="grid gap-4 md:grid-cols-5">
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str($status)->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report_id" class="admin-label">Report ID</label>
                    <input id="report_id" name="report_id" value="{{ $filters['report_id'] ?? '' }}" class="admin-control mt-1">
                </div>
                <div>
                    <label for="claimant_id" class="admin-label">Claimant ID</label>
                    <input id="claimant_id" name="claimant_id" value="{{ $filters['claimant_id'] ?? '' }}" class="admin-control mt-1">
                </div>
                <div>
                    <label for="sort_dir" class="admin-label">Order</label>
                    <select id="sort_dir" name="sort_dir" class="admin-control mt-1">
                        <option value="desc" @selected(($filters['sort_dir'] ?? 'desc') === 'desc')>Newest first</option>
                        <option value="asc" @selected(($filters['sort_dir'] ?? '') === 'asc')>Oldest first</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Apply</x-ui.button>
                    <x-ui.button :href="route('admin.claims.index')" variant="secondary">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Claims" subtitle="Moderate ownership proof and resolve report matches">
            @if ($claims->isEmpty())
                <x-ui.empty-state title="No claims found" message="Claim submissions matching these filters will appear here." />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Report</th>
                                <th class="admin-table-heading">Claimant</th>
                                <th class="admin-table-heading">Proof</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Submitted</th>
                                <th class="admin-table-heading text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($claims as $claim)
                                <tr>
                                    <td class="admin-table-cell">
                                        <a href="{{ route('admin.claims.show', $claim) }}" class="font-medium text-gray-950 hover:text-emerald-700">
                                            {{ $claim->report?->title ?? 'Deleted report' }}
                                        </a>
                                        <p class="mt-1 text-xs text-gray-500">Report #{{ $claim->report_id }}</p>
                                    </td>
                                    <td class="admin-table-cell">
                                        <p class="font-medium text-gray-900">{{ $claim->claimant?->name }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $claim->claimant?->email }}</p>
                                    </td>
                                    <td class="admin-table-cell max-w-md">{{ str($claim->proof_text)->limit(140) }}</td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$claim->status?->value ?? $claim->status" /></td>
                                    <td class="admin-table-cell text-gray-500">{{ $claim->created_at->format('M j, Y') }}</td>
                                    <td class="admin-table-cell">
                                        <div class="flex justify-end gap-2">
                                            <x-ui.button :href="route('admin.claims.show', $claim)" variant="secondary" size="sm">Open</x-ui.button>
                                            @if (($claim->status?->value ?? $claim->status) === 'pending')
                                                <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button size="sm" data-loading-button data-loading-text="Approving...">Approve</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" data-loading-form>
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
                    {{ $claims->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
