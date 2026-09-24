@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-brand text-on-brand hover:bg-brand/90',
        // Outline: amber on hover, always with navy text (never white on amber).
        'secondary' => 'border border-line-strong bg-surface text-strong hover:border-amber hover:bg-amber hover:text-navy',
        'ghost' => 'text-fg hover:bg-brand/6 hover:text-strong',
        'danger' => 'border border-error/40 bg-surface text-error hover:border-error hover:bg-error/6 dark:text-red-400 dark:border-red-400/40',
    ];

    $sizes = [
        'sm' => 'h-9 px-3.5 text-sm',
        'md' => 'h-10 px-4.5 text-sm',
        'lg' => 'h-12 px-6 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold whitespace-nowrap '
        .'transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand '
        .'disabled:pointer-events-none disabled:opacity-45 [&_svg]:size-4 [&_svg]:shrink-0 '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
