@props(['active' => false])

@php
    $classes = 'inline-flex h-8 items-center rounded-full border px-3.5 text-sm font-medium transition-colors duration-150 '
        .'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand '
        .($active
            ? 'border-brand bg-brand text-on-brand'
            : 'border-line-strong bg-surface text-fg hover:border-brand/40 hover:text-strong');
@endphp

<button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
