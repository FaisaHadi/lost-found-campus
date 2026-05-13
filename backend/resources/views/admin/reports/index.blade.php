@extends('layouts.admin')

@section('title', 'Manajemen Laporan')

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filter" subtitle="Cari dan kelompokkan laporan berdasarkan kriteria moderasi">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label for="keyword" class="admin-label">Cari</label>
                    <input id="keyword" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="admin-control mt-1" placeholder="Judul, deskripsi, lokasi">
                </div>
                <div>
                    <label for="category_id" class="admin-label">Kategori</label>
                    <select id="category_id" name="category_id" class="admin-control mt-1">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report_type" class="admin-label">Jenis</label>
                    <select id="report_type" name="report_type" class="admin-control mt-1">
                        <option value="">Semua jenis</option>
                        <option value="lost" @selected(($filters['report_type'] ?? '') === 'lost')>Hilang</option>
                        <option value="found" @selected(($filters['report_type'] ?? '') === 'found')>Ditemukan</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">Semua status</option>
                        @foreach (['pending', 'approved', 'rejected', 'claimed', 'completed'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                                {{ [
                                    'pending' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'claimed' => 'Diklaim',
                                    'completed' => 'Selesai',
                                ][$status] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Terapkan</x-ui.button>
                    <x-ui.button :href="route('admin.reports.index')" variant="secondary">Atur ulang</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Laporan" subtitle="Tinjau kiriman, lihat detail, dan moderasi status laporan">
            @if ($reports->isEmpty())
                <x-ui.empty-state title="Laporan tidak ditemukan" message="Coba ubah filter atau tunggu kiriman baru." />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Laporan</th>
                                <th class="admin-table-heading">Pelapor</th>
                                <th class="admin-table-heading">Jenis</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Moderasi</th>
                                <th class="admin-table-heading">Dikirim</th>
                                <th class="admin-table-heading text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="admin-table-cell">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="font-medium text-gray-950 hover:text-emerald-700">{{ $report->title }}</a>
                                        <p class="mt-1 text-xs text-gray-500">{{ $report->category?->name ?? 'Tanpa kategori' }} - {{ $report->location_text ?: 'Tidak ada catatan lokasi' }}</p>
                                    </td>
                                    <td class="admin-table-cell">{{ $report->user?->name }}</td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->report_type?->value ?? $report->report_type" /></td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->status?->value ?? $report->status" /></td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$report->moderation_status?->value ?? $report->moderation_status" /></td>
                                    <td class="admin-table-cell text-gray-500">{{ $report->created_at->format('M j, Y') }}</td>
                                    <td class="admin-table-cell">
                                        <div class="flex justify-end gap-2">
                                            <x-ui.button :href="route('admin.reports.show', $report)" variant="secondary" size="sm">Buka</x-ui.button>
                                            @if (($report->moderation_status?->value ?? $report->moderation_status) === 'pending')
                                                <form method="POST" action="{{ route('admin.reports.approve', $report) }}" data-loading-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button size="sm" data-loading-button data-loading-text="Menyetujui...">Setujui</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reports.reject', $report) }}" data-loading-form>
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
                    {{ $reports->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
