@extends('layouts.admin')

@section('title', 'Claim Detail')

@section('page-actions')
    <x-ui.button :href="route('admin.claims.index')" variant="secondary">Back to claims</x-ui.button>
@endsection

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-ui.card title="Ownership proof" subtitle="Read the claimant explanation before taking action">
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <x-ui.badge :value="$claim->status?->value ?? $claim->status" />
                    @if ($claim->reviewed_at)
                        <span class="text-sm text-gray-500">Reviewed {{ $claim->reviewed_at->diffForHumans() }} by {{ $claim->reviewer?->name }}</span>
                    @endif
                </div>
                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">{{ $claim->proof_text }}</p>
            </x-ui.card>

            <x-ui.card title="Related report" subtitle="Report context used during claim review">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Report title</p>
                        @if ($claim->report)
                            <a href="{{ route('admin.reports.show', $claim->report) }}" class="mt-1 block text-base font-semibold text-gray-950 hover:text-emerald-700">
                                {{ $claim->report->title }}
                            </a>
                        @else
                            <p class="mt-1 text-base font-semibold text-gray-950">Deleted report</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Report owner</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $claim->report?->user?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $claim->report?->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Report status</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-ui.badge :value="$claim->report?->status?->value ?? $claim->report?->status" />
                            <x-ui.badge :value="$claim->report?->report_type?->value ?? $claim->report?->report_type" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Location</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $claim->report?->location_text ?: 'No location text' }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Claimant" subtitle="Account submitting the proof">
                <p class="text-base font-semibold text-gray-950">{{ $claim->claimant?->name }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $claim->claimant?->email }}</p>
                <p class="mt-4 text-xs font-medium uppercase text-gray-500">Submitted</p>
                <p class="mt-1 text-sm text-gray-900">{{ $claim->created_at->format('M j, Y H:i') }}</p>
            </x-ui.card>

            <x-ui.card title="Review action" subtitle="Approving a claim marks the report as claimed">
                @if (($claim->status?->value ?? $claim->status) === 'pending')
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" data-loading-form>
                            @csrf
                            @method('PATCH')
                            <x-ui.button data-loading-button data-loading-text="Approving...">Approve claim</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" data-loading-form>
                            @csrf
                            @method('PATCH')
                            <x-ui.button variant="danger" data-loading-button data-loading-text="Rejecting...">Reject claim</x-ui.button>
                        </form>
                    </div>
                @else
                    <x-ui.empty-state title="Review complete" message="Only pending claims can be approved or rejected." />
                @endif
            </x-ui.card>
        </div>
    </div>
@endsection
