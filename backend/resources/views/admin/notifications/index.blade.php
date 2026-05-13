@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filter" subtitle="Pantau pesan sistem yang belum dibaca dan sudah dibaca">
            <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid gap-4 md:grid-cols-4">
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">Semua status</option>
                        <option value="unread" @selected(($filters['status'] ?? '') === 'unread')>Belum dibaca</option>
                        <option value="read" @selected(($filters['status'] ?? '') === 'read')>Dibaca</option>
                    </select>
                </div>
                <div>
                    <label for="sort_dir" class="admin-label">Urutan</label>
                    <select id="sort_dir" name="sort_dir" class="admin-control mt-1">
                        <option value="desc" @selected(($filters['sort_dir'] ?? 'desc') === 'desc')>Terbaru dulu</option>
                        <option value="asc" @selected(($filters['sort_dir'] ?? '') === 'asc')>Terlama dulu</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2">
                    <x-ui.button type="submit">Terapkan</x-ui.button>
                    <x-ui.button :href="route('admin.notifications.index')" variant="secondary">Atur ulang</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Linimasa notifikasi" subtitle="Pesan moderasi dan perubahan status untuk akun admin ini">
            @if ($notifications->isEmpty())
                <x-ui.empty-state title="Notifikasi tidak ditemukan" message="Notifikasi moderasi laporan dan klaim akan muncul di sini." />
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
                                        <x-ui.button :href="route('admin.reports.show', $notification->report)" variant="secondary" size="sm">Laporan</x-ui.button>
                                    @endif
                                    @if ($notification->claim)
                                        <x-ui.button :href="route('admin.claims.show', $notification->claim)" variant="secondary" size="sm">Klaim</x-ui.button>
                                    @endif
                                    @if (($notification->status?->value ?? $notification->status) === 'unread')
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" data-loading-form>
                                            @csrf
                                            @method('PATCH')
                                            <x-ui.button size="sm" data-loading-button data-loading-text="Menandai...">Tandai dibaca</x-ui.button>
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
