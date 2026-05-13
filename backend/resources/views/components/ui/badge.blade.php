@props(['value' => null, 'variant' => null])

@php
    $labels = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'claimed' => 'Diklaim',
        'completed' => 'Selesai',
        'lost' => 'Hilang',
        'found' => 'Ditemukan',
        'active' => 'Aktif',
        'inactive' => 'Tidak aktif',
        'unread' => 'Belum dibaca',
        'read' => 'Dibaca',
    ];
    $label = $value ? ($labels[$value] ?? str($value)->replace('_', ' ')->title()) : trim($slot);
    $key = $variant ?? $value;
    $classes = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'claimed' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'completed' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'lost' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'found' => 'bg-teal-50 text-teal-700 ring-teal-200',
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'inactive' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'unread' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'read' => 'bg-gray-100 text-gray-700 ring-gray-200',
    ][$key] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset '.$classes]) }}>
    {{ $label }}
</span>
