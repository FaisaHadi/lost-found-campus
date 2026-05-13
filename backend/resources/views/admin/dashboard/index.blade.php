@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.card>
                <p class="text-sm font-medium text-gray-500">Total reports</p>
                <p class="mt-2 text-3xl font-semibold text-gray-950">{{ number_format($stats['total_reports']) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-sm font-medium text-gray-500">Pending reports</p>
                <p class="mt-2 text-3xl font-semibold text-amber-600">{{ number_format($stats['pending_reports']) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-sm font-medium text-gray-500">Pending claims</p>
                <p class="mt-2 text-3xl font-semibold text-sky-600">{{ number_format($stats['pending_claims']) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-sm font-medium text-gray-500">Approved reports</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ number_format($stats['approved_reports']) }}</p>
            </x-ui.card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-ui.card title="Moderation queue" subtitle="Newest reports waiting for admin attention" class="xl:col-span-2">
                @if ($recentReports->isEmpty())
                    <x-ui.empty-state title="No reports yet" message="Submitted reports will appear here for review." />
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="admin-table-heading">Report</th>
                                    <th class="admin-table-heading">Type</th>
                                    <th class="admin-table-heading">Status</th>
                                    <th class="admin-table-heading">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentReports as $report)
                                    <tr>
                                        <td class="admin-table-cell">
                                            <a href="{{ route('admin.reports.show', $report) }}" class="font-medium text-gray-950 hover:text-emerald-700">{{ $report->title }}</a>
                                            <p class="mt-1 text-xs text-gray-500">{{ $report->user?->name }} · {{ $report->category?->name ?? 'Uncategorized' }}</p>
                                        </td>
                                        <td class="admin-table-cell"><x-ui.badge :value="$report->report_type?->value ?? $report->report_type" /></td>
                                        <td class="admin-table-cell"><x-ui.badge :value="$report->status?->value ?? $report->status" /></td>
                                        <td class="admin-table-cell text-gray-500">{{ $report->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Operational snapshot" subtitle="Quick management totals">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-600">Categories</span>
                        <span class="text-sm font-semibold text-gray-950">{{ number_format($stats['categories']) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-600">Registered users</span>
                        <span class="text-sm font-semibold text-gray-950">{{ number_format($stats['users']) }}</span>
                    </div>
                    @foreach (['pending', 'approved', 'rejected', 'claimed', 'completed'] as $status)
                        <div class="flex items-center justify-between">
                            <x-ui.badge :value="$status" />
                            <span class="text-sm font-semibold text-gray-950">{{ number_format($reportStatusCounts[$status] ?? 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.card title="Recent claims" subtitle="Latest ownership proof submitted by users">
                @forelse ($recentClaims as $claim)
                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-3 first:pt-0 last:border-b-0 last:pb-0">
                        <div>
                            <a href="{{ route('admin.claims.show', $claim) }}" class="text-sm font-medium text-gray-950 hover:text-emerald-700">
                                {{ $claim->report?->title ?? 'Deleted report' }}
                            </a>
                            <p class="mt-1 text-xs text-gray-500">{{ $claim->claimant?->name }} · {{ $claim->created_at->diffForHumans() }}</p>
                        </div>
                        <x-ui.badge :value="$claim->status?->value ?? $claim->status" />
                    </div>
                @empty
                    <x-ui.empty-state title="No claims yet" message="Claim submissions will appear here." />
                @endforelse
            </x-ui.card>

            <x-ui.card title="Unread notifications" subtitle="System messages generated by moderation actions">
                @forelse ($recentNotifications as $notification)
                    <div class="border-b border-gray-100 py-3 first:pt-0 last:border-b-0 last:pb-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-gray-950">{{ $notification->title }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $notification->message }}</p>
                            </div>
                            <x-ui.badge :value="$notification->status?->value ?? $notification->status" />
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="All clear" message="Unread admin notifications will appear here." />
                @endforelse
            </x-ui.card>
        </div>
    </div>
@endsection
