@props(['title' => 'Belum ada data', 'message' => 'Saat ini belum ada yang dapat ditampilkan.'])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center']) }}>
    <p class="text-sm font-semibold text-gray-900">{{ $title }}</p>
    <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
    @if (trim($slot) !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
