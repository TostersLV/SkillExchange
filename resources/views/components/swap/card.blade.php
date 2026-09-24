@props(['interactive' => false, 'padding' => 'md'])

@php
    $paddings = [
        'none' => '',
        'md' => 'p-5 sm:p-6',
        'lg' => 'p-6 sm:p-8',
    ];

    $classes = 'rounded-xl border border-line bg-surface card-shadow '.($paddings[$padding] ?? $paddings['md']);

    if ($interactive) {
        $classes .= ' transition-[box-shadow,border-color] duration-200 group-hover:border-line-strong group-hover:card-shadow-hover group-focus-visible:border-line-strong';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
