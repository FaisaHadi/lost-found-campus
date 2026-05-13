@php
    $user = auth()->user();
    $unreadCount = $user->appNotifications()->where('status', 'unread')->count();
    $notifications = $user->appNotifications()->latest()->limit(5)->get();
@endphp

<header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <button type="button" data-sidebar-toggle class="admin-focus rounded-md p-2 text-gray-600 hover:bg-gray-100 lg:hidden" aria-label="Buka navigasi">
            <span class="block h-0.5 w-5 bg-current"></span>
            <span class="mt-1 block h-0.5 w-5 bg-current"></span>
            <span class="mt-1 block h-0.5 w-5 bg-current"></span>
        </button>

        <div class="hidden min-w-0 md:block">
            <p class="text-sm font-medium text-gray-950">Platform Administrasi dan Moderasi</p>
            <p class="text-xs text-gray-500">Backend Laravel bersama untuk alur kerja admin web</p>
        </div>

        <div class="ml-auto flex items-center gap-3">
            <div class="relative">
                <button type="button" data-notification-toggle class="admin-focus relative rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Notifikasi
                    @if ($unreadCount > 0)
                        <span class="ml-2 rounded-full bg-amber-500 px-2 py-0.5 text-xs text-white">{{ $unreadCount }}</span>
                    @endif
                </button>

                <div data-notification-menu class="absolute right-0 mt-2 hidden w-80 rounded-lg border border-gray-200 bg-white shadow-lg data-open:block">
                    <div class="border-b border-gray-200 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-950">Notifikasi terbaru</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse ($notifications as $notification)
                            <div class="border-b border-gray-100 px-4 py-3 last:border-b-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-950">{{ $notification->title }}</p>
                                        <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $notification->message }}</p>
                                    </div>
                                    <x-ui.badge :value="$notification->status?->value ?? $notification->status" />
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</div>
                        @endforelse
                    </div>
                    <div class="border-t border-gray-200 p-3">
                        <a href="{{ route('admin.notifications.index') }}" class="admin-focus block rounded-md px-3 py-2 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}" data-loading-form>
                @csrf
                <x-ui.button variant="secondary" type="submit" data-loading-button data-loading-text="Keluar..." size="sm">
                    Keluar
                </x-ui.button>
            </form>
        </div>
    </div>
</header>
