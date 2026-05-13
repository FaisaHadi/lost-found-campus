@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'admin-surface']) }}>
    @if ($title || $subtitle || isset($actions))
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                @if ($title)
                    <h2 class="text-base font-semibold text-gray-950">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div class="p-5">
        {{ $slot }}
    </div>
</section>
