@php
    $items = [
        ['label' => 'Dasbor', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Laporan', 'route' => 'admin.reports.index', 'active' => 'admin.reports.*'],
        ['label' => 'Klaim', 'route' => 'admin.claims.index', 'active' => 'admin.claims.*'],
        ['label' => 'Kategori', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'],
        ['label' => 'Notifikasi', 'route' => 'admin.notifications.index', 'active' => 'admin.notifications.*'],
    ];
@endphp

<aside data-admin-sidebar class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-gray-200 bg-white data-open:block lg:block">
    <div class="flex h-full flex-col">
        <div class="border-b border-gray-200 px-6 py-5">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <p class="text-sm font-semibold uppercase text-emerald-700">Lost & Found</p>
                <p class="mt-1 text-lg font-semibold text-gray-950">Admin Kampus</p>
            </a>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4">
            @foreach ($items as $item)
                @php
                    $active = request()->routeIs($item['active']);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="{{ $active ? 'bg-emerald-50 text-emerald-800' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-950' }} admin-focus flex items-center rounded-md px-3 py-2 text-sm font-medium"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-gray-200 p-4">
            <div class="rounded-lg bg-gray-50 p-3">
                <p class="text-sm font-medium text-gray-950">{{ auth()->user()->name }}</p>
                <p class="mt-1 truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
