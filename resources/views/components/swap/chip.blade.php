@props(['active' => false])

@php
    $classes = 'inline-flex h-10 items-center gap-2 rounded-full border px-4 text-sm font-semibold transition-[color,background-color,border-color,box-shadow] duration-150 '
        .'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand '
        .($active
            ? 'border-brand bg-brand text-on-brand shadow-[0_8px_20px_-10px_color-mix(in_oklab,var(--color-brand)_60%,transparent)]'
            : 'border-line bg-surface text-fg hover:border-line-strong hover:text-strong hover:shadow-sm');
@endphp

<button type="button" aria-pressed="{{ $active ? 'true' : 'false' }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
