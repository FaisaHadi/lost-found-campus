@extends('layouts.admin')

@section('title', 'Manajemen Klaim')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filter" subtitle="Tinjau klaim berdasarkan status, laporan, atau pengaju">
            <form method="GET" action="{{ route('admin.claims.index') }}" class="grid gap-4 md:grid-cols-5">
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">Semua status</option>
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                {{ [
                                    'pending' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                ][$status] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report_id" class="admin-label">ID Laporan</label>
                    <input id="report_id" name="report_id" value="{{ $filters['report_id'] ?? '' }}" class="admin-control mt-1">
                </div>
                <div>
                    <label for="claimant_id" class="admin-label">ID Pengaju</label>
                    <input id="claimant_id" name="claimant_id" value="{{ $filters['claimant_id'] ?? '' }}" class="admin-control mt-1">
                </div>
                <div>
                    <label for="sort_dir" class="admin-label">Urutan</label>
                    <select id="sort_dir" name="sort_dir" class="admin-control mt-1">
                        <option value="desc" @selected(($filters['sort_dir'] ?? 'desc') === 'desc')>Terbaru dulu</option>
                        <option value="asc" @selected(($filters['sort_dir'] ?? '') === 'asc')>Terlama dulu</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Terapkan</x-ui.button>
                    <x-ui.button :href="route('admin.claims.index')" variant="secondary">Atur ulang</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Klaim" subtitle="Moderasi bukti kepemilikan dan cocokkan dengan laporan">
            @if ($claims->isEmpty())
                <x-ui.empty-state title="Klaim tidak ditemukan" message="Klaim yang sesuai filter akan muncul di sini." />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Laporan</th>
                                <th class="admin-table-heading">Pengaju</th>
                                <th class="admin-table-heading">Bukti</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Dikirim</th>
                                <th class="admin-table-heading text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($claims as $claim)
                                <tr>
                                    <td class="admin-table-cell">
                                        <a href="{{ route('admin.claims.show', $claim) }}" class="font-medium text-gray-950 hover:text-emerald-700">
                                            {{ $claim->report?->title ?? 'Laporan dihapus' }}
                                        </a>
                                        <p class="mt-1 text-xs text-gray-500">Laporan #{{ $claim->report_id }}</p>
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
                                            <x-ui.button :href="route('admin.claims.show', $claim)" variant="secondary" size="sm">Buka</x-ui.button>
                                            @if (($claim->status?->value ?? $claim->status) === 'pending')
                                                <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button size="sm" data-loading-button data-loading-text="Menyetujui...">Setujui</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button variant="danger" size="sm" data-loading-button data-loading-text="Menolak...">Tolak</x-ui.button>
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
