<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#047857">
        <link rel="manifest" href="/manifest.json">
        <title>@yield('title', 'Dasbor') - Admin Lost & Found Campus</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900 antialiased">
        <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-gray-950/40 data-open:block lg:hidden"></div>

        <div class="min-h-screen lg:flex">
            <x-admin.sidebar />

            <div class="min-w-0 flex-1 lg:pl-72">
                <x-admin.topbar />

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-700">Administrasi</p>
                            <h1 class="mt-1 text-2xl font-semibold text-gray-950">@yield('title', 'Dasbor')</h1>
                        </div>
                        @hasSection('page-actions')
                            <div class="flex flex-wrap items-center gap-2">
                                @yield('page-actions')
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @if (session('success'))
                            <x-ui.alert>{{ session('success') }}</x-ui.alert>
                        @endif

                        @if (session('status'))
                            <x-ui.alert type="warning">{{ session('status') }}</x-ui.alert>
                        @endif

                        @if ($errors->any())
                            <x-ui.alert type="error">
                                <p class="font-medium">Periksa kembali kolom yang ditandai.</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.alert>
                        @endif
                    </div>

                    <div class="mt-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
