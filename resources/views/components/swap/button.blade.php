@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-brand text-on-brand shadow-[0_8px_20px_-8px_color-mix(in_oklab,var(--color-brand)_55%,transparent)] hover:-translate-y-px hover:bg-brand/90 hover:shadow-[0_12px_24px_-8px_color-mix(in_oklab,var(--color-brand)_60%,transparent)]',
        // Emerald: the "make a swap" call to action (post an exchange, propose a swap).
        'swap' => 'bg-swap-strong text-white shadow-[0_8px_20px_-8px_color-mix(in_oklab,var(--color-swap-strong)_60%,transparent)] hover:-translate-y-px hover:bg-[#065f46]',
        'secondary' => 'border border-line-strong bg-surface text-strong hover:-translate-y-px hover:border-strong/40 hover:bg-raised',
        'ghost' => 'text-fg hover:bg-brand/6 hover:text-strong',
        'danger' => 'border border-error/40 bg-surface text-error hover:border-error hover:bg-error/6 dark:text-red-400 dark:border-red-400/40',
        // For navy backgrounds (landing hero / call-to-action).
        'inverse' => 'bg-paper text-navy shadow-[0_8px_24px_-10px_rgba(0,0,0,0.5)] hover:-translate-y-px hover:bg-white focus-visible:outline-paper',
        'outline-inverse' => 'border border-paper/30 text-paper hover:border-paper/60 hover:bg-paper/10 focus-visible:outline-paper',
    ];

    $sizes = [
        'sm' => 'h-9 px-4 text-sm',
        'md' => 'h-11 px-5 text-sm',
        'lg' => 'h-13 px-7 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold whitespace-nowrap '
        .'transition-[color,background-color,border-color,box-shadow,translate] duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand '
        .'disabled:pointer-events-none disabled:opacity-45 [&_svg]:size-4 [&_svg]:shrink-0 '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($attributes->has('href'))
    @php
        // Internal page links use Livewire's SPA navigation (no full reload, no style flash); in-page anchors don't.
        $href = (string) $attributes->get('href');
        $isInternalPage = ! str_contains($href, '#') && (str_starts_with($href, '/') || str_starts_with($href, url('/')));
    @endphp
    <a {{ $attributes->merge(['class' => $classes]) }} @if ($isInternalPage && ! $attributes->has('wire:navigate')) wire:navigate @endif>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
