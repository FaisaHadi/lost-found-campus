@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Kembali ke kategori</x-ui.button>
@endsection

@section('content')
    <div class="max-w-2xl">
        <x-ui.card title="Detail kategori" subtitle="Jaga nama, status, dan deskripsi agar jelas untuk semua klien platform">
            @include('admin.categories._form', [
                'action' => route('admin.categories.update', $category),
                'method' => 'PUT',
                'submitLabel' => 'Simpan kategori',
            ])
        </x-ui.card>
    </div>
@endsection
