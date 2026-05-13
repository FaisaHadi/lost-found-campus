@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filters" subtitle="Monitor unread and read system messages">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid gap-4 md:grid-cols-4">
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">All statuses</option>
                        <option value="unread" @selected(($filters['status'] ?? '') === 'unread')>Unread</option>
                        <option value="read" @selected(($filters['status'] ?? '') === 'read')>Read</option>
                    </select>
                </div>
                <div>
                    <label for="sort_dir" class="admin-label">Order</label>
                    <select id="sort_dir" name="sort_dir" class="admin-control mt-1">
                        <option value="desc" @selected(($filters['sort_dir'] ?? 'desc') === 'desc')>Newest first</option>
                        <option value="asc" @selected(($filters['sort_dir'] ?? '') === 'asc')>Oldest first</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2">
                    <x-ui.button type="submit">Apply</x-ui.button>
                    <x-ui.button :href="route('admin.notifications.index')" variant="secondary">Reset</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Notification feed" subtitle="Moderation and lifecycle messages for this admin account">
            @if ($notifications->isEmpty())
                <x-ui.empty-state title="No notifications found" message="Report and claim moderation notifications will appear here." />
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($notifications as $notification)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-950">{{ $notification->title }}</p>
                                        <x-ui.badge :value="$notification->status?->value ?? $notification->status" />
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $notification->message }}</p>
                                    <p class="mt-2 text-xs text-gray-500">{{ $notification->created_at->format('M j, Y H:i') }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    @if ($notification->report)
                                        <x-ui.button :href="route('admin.reports.show', $notification->report)" variant="secondary" size="sm">Report</x-ui.button>
                                    @endif
                                    @if ($notification->claim)
                                        <x-ui.button :href="route('admin.claims.show', $notification->claim)" variant="secondary" size="sm">Claim</x-ui.button>
                                    @endif
                                    @if (($notification->status?->value ?? $notification->status) === 'unread')
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" data-loading-form>
                                            @csrf
                                            @method('PATCH')
                                            <x-ui.button size="sm" data-loading-button data-loading-text="Marking...">Mark read</x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $notifications->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
