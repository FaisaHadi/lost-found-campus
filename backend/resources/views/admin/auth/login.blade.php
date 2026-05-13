@extends('layouts.guest')

@section('title', 'Masuk Admin')

@section('content')
    <section class="w-full max-w-md">
        <div class="mb-6 text-center">
            <p class="text-sm font-semibold uppercase text-emerald-700">Lost & Found Campus</p>
            <h1 class="mt-2 text-2xl font-semibold text-gray-950">Masuk admin</h1>
            <p class="mt-2 text-sm text-gray-600">Akses alat moderasi, pengelolaan, analitik, dan pemantauan.</p>
        </div>

        <div class="admin-surface p-6">
            @if (session('status'))
                <x-ui.alert type="warning" class="mb-4">{{ session('status') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error" class="mb-4">
                    {{ $errors->first() }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4" data-loading-form>
                @csrf

                <div>
                    <label for="email" class="admin-label">Alamat email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" class="admin-control mt-1" required autofocus>
                </div>

                <div>
                    <label for="password" class="admin-label">Kata sandi</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" class="admin-control mt-1" required>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" value="1" class="admin-focus rounded border-gray-300 text-emerald-600">
                    Tetap masuk
                </label>

                <x-ui.button class="w-full" data-loading-button data-loading-text="Masuk...">
                    Masuk
                </x-ui.button>
            </form>
        </div>
    </section>
@endsection
