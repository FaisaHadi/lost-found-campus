@props(['type' => 'success'])

@php
    $classes = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
    ][$type] ?? 'border-gray-200 bg-gray-50 text-gray-800';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border px-4 py-3 text-sm '.$classes]) }}>
    {{ $slot }}
</div>
