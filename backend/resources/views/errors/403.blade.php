@extends(auth()->check() ? 'layouts.admin' : 'layouts.guest')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-ui.empty-state title="Akses ditolak" message="Akun Anda tidak memiliki izin untuk membuka area administrasi ini.">
            @auth
                <x-ui.button :href="route('admin.dashboard')" variant="secondary">Kembali ke dasbor</x-ui.button>
            @else
                <x-ui.button :href="route('admin.login')">Masuk</x-ui.button>
            @endauth
        </x-ui.empty-state>
    </div>
@endsection
