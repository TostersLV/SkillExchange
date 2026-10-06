@props(['interactive' => false, 'padding' => 'md'])

@php
    $paddings = [
        'none' => '',
        'md' => 'p-6 sm:p-7',
        'lg' => 'p-7 sm:p-10',
    ];

    $classes = 'rounded-[1.25rem] border border-line bg-surface card-shadow '.($paddings[$padding] ?? $paddings['md']);

    if ($interactive) {
        $classes .= ' transition-[box-shadow,border-color,translate] duration-200 group-hover:-translate-y-1 group-hover:border-line-strong group-hover:card-shadow-hover group-focus-visible:border-line-strong';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
