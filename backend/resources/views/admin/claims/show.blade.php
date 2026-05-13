@extends('layouts.admin')

@section('title', 'Detail Klaim')

@section('page-actions')
    <x-ui.button :href="route('admin.claims.index')" variant="secondary">Kembali ke klaim</x-ui.button>
@endsection

@section('content')
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-ui.card title="Bukti kepemilikan" subtitle="Baca penjelasan pengaju sebelum mengambil tindakan">
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    <x-ui.badge :value="$claim->status?->value ?? $claim->status" />
                    @if ($claim->reviewed_at)
                        <span class="text-sm text-gray-500">Ditinjau {{ $claim->reviewed_at->diffForHumans() }} oleh {{ $claim->reviewer?->name }}</span>
                    @endif
                </div>
                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">{{ $claim->proof_text }}</p>
            </x-ui.card>

            <x-ui.card title="Laporan terkait" subtitle="Konteks laporan yang digunakan saat meninjau klaim">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Judul laporan</p>
                        @if ($claim->report)
                            <a href="{{ route('admin.reports.show', $claim->report) }}" class="mt-1 block text-base font-semibold text-gray-950 hover:text-emerald-700">
                                {{ $claim->report->title }}
                            </a>
                        @else
                            <p class="mt-1 text-base font-semibold text-gray-950">Laporan dihapus</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pemilik laporan</p>
                        <p class="mt-1 text-base font-semibold text-gray-950">{{ $claim->report?->user?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $claim->report?->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status laporan</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <x-ui.badge :value="$claim->report?->status?->value ?? $claim->report?->status" />
                            <x-ui.badge :value="$claim->report?->report_type?->value ?? $claim->report?->report_type" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Lokasi</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $claim->report?->location_text ?: 'Tidak ada catatan lokasi' }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Pengaju klaim" subtitle="Akun yang mengirim bukti">
                <p class="text-base font-semibold text-gray-950">{{ $claim->claimant?->name }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $claim->claimant?->email }}</p>
                <p class="mt-4 text-xs font-medium uppercase text-gray-500">Dikirim</p>
                <p class="mt-1 text-sm text-gray-900">{{ $claim->created_at->format('M j, Y H:i') }}</p>
            </x-ui.card>

            <x-ui.card title="Aksi tinjauan" subtitle="Menyetujui klaim akan menandai laporan sebagai diklaim">
                @if (($claim->status?->value ?? $claim->status) === 'pending')
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.claims.approve', $claim) }}" data-loading-form>
                            @csrf
                            @method('PATCH')
                            <x-ui.button data-loading-button data-loading-text="Menyetujui...">Setujui klaim</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('admin.claims.reject', $claim) }}" data-loading-form>
                            @csrf
                            @method('PATCH')
                            <x-ui.button variant="danger" data-loading-button data-loading-text="Menolak...">Tolak klaim</x-ui.button>
                        </form>
                    </div>
                @else
                    <x-ui.empty-state title="Tinjauan selesai" message="Hanya klaim berstatus menunggu yang dapat disetujui atau ditolak." />
                @endif
            </x-ui.card>
        </div>
    </div>
@endsection
