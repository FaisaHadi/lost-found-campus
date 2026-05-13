@extends('layouts.admin')

@section('title', 'Buat Kategori')

@section('page-actions')
    <x-ui.button :href="route('admin.categories.index')" variant="secondary">Kembali ke kategori</x-ui.button>
@endsection

@section('content')
    <div class="max-w-2xl">
        <x-ui.card title="Detail kategori" subtitle="Kategori membantu admin dan pengguna menyaring laporan secara konsisten">
            @include('admin.categories._form', [
                'action' => route('admin.categories.store'),
                'method' => 'POST',
                'submitLabel' => 'Buat kategori',
            ])
        </x-ui.card>
    </div>
@endsection
