@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.create')">Buat kategori</x-ui.button>
@endsection

@section('content')
    <div class="space-y-6">
        <x-ui.card title="Filter" subtitle="Kelola ketersediaan kategori untuk penyaringan laporan">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="keyword" class="admin-label">Cari</label>
                    <input id="keyword" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="admin-control mt-1" placeholder="Nama, slug, deskripsi">
                </div>
                <div>
                    <label for="status" class="admin-label">Status</label>
                    <select id="status" name="status" class="admin-control mt-1">
                        <option value="">Semua status</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Tidak aktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit">Terapkan</x-ui.button>
                    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Atur ulang</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Kategori" subtitle="Kategori bersama untuk admin web dan aplikasi mobile">
            @if ($categories->isEmpty())
                <x-ui.empty-state title="Kategori tidak ditemukan" message="Buat kategori pertama untuk mulai mengelompokkan laporan.">
                    <x-ui.button :href="route('admin.categories.create')">Buat kategori</x-ui.button>
                </x-ui.empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="admin-table-heading">Nama</th>
                                <th class="admin-table-heading">Slug</th>
                                <th class="admin-table-heading">Status</th>
                                <th class="admin-table-heading">Diperbarui</th>
                                <th class="admin-table-heading text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="admin-table-cell">
                                        <p class="font-medium text-gray-950">{{ $category->name }}</p>
                                        <p class="mt-1 max-w-xl text-xs text-gray-500">{{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                                    </td>
                                    <td class="admin-table-cell text-gray-500">{{ $category->slug }}</td>
                                    <td class="admin-table-cell"><x-ui.badge :value="$category->status" /></td>
                                    <td class="admin-table-cell text-gray-500">{{ $category->updated_at->format('M j, Y') }}</td>
                                    <td class="admin-table-cell">
                                        <div class="flex justify-end gap-2">
                                            <x-ui.button :href="route('admin.categories.edit', $category)" variant="secondary" size="sm">Edit</x-ui.button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-loading-form>
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" size="sm" data-loading-button data-loading-text="Menghapus...">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $categories->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
