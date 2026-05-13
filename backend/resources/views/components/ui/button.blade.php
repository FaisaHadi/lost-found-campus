@props([
    'href' => null,
    'variant' => 'primary',
    'type' => 'submit',
    'size' => 'md',
])

@php
    $base = 'admin-focus inline-flex items-center justify-center gap-2 rounded-md font-medium transition disabled:cursor-not-allowed disabled:opacity-60';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
    ];
    $variants = [
        'primary' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600',
        'ghost' => 'text-gray-600 hover:bg-gray-100',
    ];
    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
