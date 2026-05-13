<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#047857">
        <title>Lost & Found Campus</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-10">
            <section class="admin-surface max-w-xl p-6 text-center">
                <p class="text-sm font-semibold uppercase text-emerald-700">Lost & Found Campus</p>
                <h1 class="mt-2 text-2xl font-semibold text-gray-950">Platform laporan barang hilang dan ditemukan</h1>
                <p class="mt-3 text-sm leading-6 text-gray-600">
                    Gunakan halaman admin untuk memoderasi laporan, meninjau klaim, dan memantau notifikasi sistem.
                </p>
                <div class="mt-5">
                    <x-ui.button :href="route('admin.dashboard')">Buka dasbor admin</x-ui.button>
                </div>
            </section>
        </main>
    </body>
</html>
