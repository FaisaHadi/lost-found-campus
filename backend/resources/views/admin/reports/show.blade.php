@extends('layouts.admin')

@section('title', 'Detail Laporan')

@section('page-actions')
    <x-ui.button :href="route('admin.reports.index')" variant="secondary">Kembali ke laporan</x-ui.button>
@endsection

@section('content')
    @php
        $imageUrl = $report->image_path ? Illuminate\Support\Facades\Storage::disk('public')->url($report->image_path) : null;
    @endphp

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-ui.card title="Ringkasan laporan" subtitle="Detail kiriman dan konteks moderasi">
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Judul</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $report->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pelapor</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $report->user?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $report->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-ui.badge :value="$report->status?->value ?? $report->status" />
                            <x-ui.badge :value="$report->moderation_status?->value ?? $report->moderation_status" />
                            <x-ui.badge :value="$report->report_type?->value ?? $report->report_type" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Lokasi</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $report->location_text ?: 'Tidak ada catatan lokasi' }}</p>
                        <p class="text-sm text-gray-500">{{ $report->latitude ?? 'Tidak ada latitude' }}, {{ $report->longitude ?? 'Tidak ada longitude' }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $report->description }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Klaim pada laporan ini" subtitle="Bukti kepemilikan yang terkait dengan laporan ini">
                @forelse ($report->claims as $claim)
                    <div class="border-b border-gray-100 py-4 first:pt-0 last:border-b-0 last:pb-0">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <a href="{{ route('admin.claims.show', $claim) }}" class="text-sm font-semibold text-gray-950 hover:text-emerald-700">
                                    {{ $claim->claimant?->name }}
                                </a>
                                <p class="mt-1 text-sm text-gray-600">{{ str($claim->proof_text)->limit(180) }}</p>
                            </div>
                            <x-ui.badge :value="$claim->status?->value ?? $claim->status" />
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="Belum ada klaim" message="Klaim pengguna akan muncul di sini setelah laporan disetujui." />
                @endforelse
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Aksi moderasi" subtitle="Setujui, tolak, atau koreksi status laporan">
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.reports.approve', $report) }}" data-loading-form>
                        @csrf
                        @method('PATCH')
                        <x-ui.button data-loading-button data-loading-text="Menyetujui...">Setujui</x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('admin.reports.reject', $report) }}" data-loading-form>
                        @csrf
                        @method('PATCH')
                        <x-ui.button variant="danger" data-loading-button data-loading-text="Menolak...">Tolak</x-ui.button>
                    </form>
                </div>
            </x-ui.card>

            <x-ui.card title="Gambar" subtitle="Bukti laporan saat ini">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" alt="Gambar laporan" class="aspect-video w-full rounded-lg border border-gray-200 object-cover">
                @else
                    <x-ui.empty-state title="Tidak ada gambar" message="Laporan ini belum memiliki bukti gambar." />
                @endif
            </x-ui.card>
        </div>
    </div>

    <x-ui.card title="Edit laporan" subtitle="Perbarui metadata, status, dan bukti gambar laporan" class="mt-6">
        <form method="POST" action="{{ route('admin.reports.update', $report) }}" enctype="multipart/form-data" class="grid gap-5 lg:grid-cols-2" data-loading-form>
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="admin-label">Judul</label>
                <input id="title" name="title" value="{{ old('title', $report->title) }}" class="admin-control mt-1" required>
            </div>

            <div>
                <label for="category_id" class="admin-label">Kategori</label>
                <select id="category_id" name="category_id" class="admin-control mt-1">
                    <option value="">Tanpa kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $report->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="report_type" class="admin-label">Jenis</label>
                <select id="report_type" name="report_type" class="admin-control mt-1">
                    <option value="lost" @selected(old('report_type', $report->report_type?->value ?? $report->report_type) === 'lost')>Hilang</option>
                    <option value="found" @selected(old('report_type', $report->report_type?->value ?? $report->report_type) === 'found')>Ditemukan</option>
                </select>
            </div>

            <div>
                <label for="status" class="admin-label">Status</label>
                <select id="status" name="status" class="admin-control mt-1">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $report->status?->value ?? $report->status) === $status)>
                            {{ [
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'claimed' => 'Diklaim',
                                'completed' => 'Selesai',
                            ][$status] ?? str($status)->title() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="latitude" class="admin-label">Latitude</label>
                <input id="latitude" name="latitude" value="{{ old('latitude', $report->latitude) }}" class="admin-control mt-1">
            </div>

            <div>
                <label for="longitude" class="admin-label">Longitude</label>
                <input id="longitude" name="longitude" value="{{ old('longitude', $report->longitude) }}" class="admin-control mt-1">
            </div>

            <div class="lg:col-span-2">
                <label for="location_text" class="admin-label">Catatan lokasi</label>
                <input id="location_text" name="location_text" value="{{ old('location_text', $report->location_text) }}" class="admin-control mt-1">
            </div>

            <div class="lg:col-span-2">
                <label for="description" class="admin-label">Deskripsi</label>
                <textarea id="description" name="description" rows="5" class="admin-control mt-1" required>{{ old('description', $report->description) }}</textarea>
            </div>

            <div class="lg:col-span-2">
                <label class="admin-label">Ganti gambar</label>
                <div data-dropzone data-max-size="4194304" class="mt-1 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-5 transition">
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    <label for="image" class="block cursor-pointer text-center">
                        <img data-dropzone-preview hidden alt="Pratinjau pilihan" class="mx-auto mb-4 aspect-video max-h-56 rounded-lg border border-gray-200 object-cover">
                        <span class="text-sm font-medium text-gray-950">Letakkan gambar di sini atau pilih dari komputer</span>
                        <span class="mt-1 block text-xs text-gray-500">JPG, PNG, atau WEBP maksimal 4 MB</span>
                        <span data-dropzone-filename class="mt-2 block text-sm text-emerald-700"></span>
                    </label>
                    <p data-dropzone-error class="mt-3 text-center text-sm text-rose-600"></p>
                </div>
                @if ($report->image_path)
                    <label class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_image" value="1" class="admin-focus rounded border-gray-300 text-emerald-600">
                        Hapus gambar saat ini tanpa mengunggah pengganti
                    </label>
                @endif
            </div>

            <div class="lg:col-span-2">
                <label for="reason" class="admin-label">Alasan penolakan atau catatan audit</label>
                <textarea id="reason" name="reason" rows="3" class="admin-control mt-1">{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 lg:col-span-2">
                <x-ui.button :href="route('admin.reports.index')" variant="secondary">Batal</x-ui.button>
                <x-ui.button data-loading-button data-loading-text="Menyimpan...">Simpan perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
