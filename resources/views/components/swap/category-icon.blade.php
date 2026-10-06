@props(['name', 'variant' => 'micro'])

@php
    // One recognizable icon per category; anything unknown falls back to a tag.
    $icon = match ($name) {
        'Business' => 'briefcase',
        'Crafts & DIY' => 'wrench-screwdriver',
        'Data & Analytics' => 'chart-bar',
        'Design' => 'paint-brush',
        'Fitness & Wellness' => 'heart',
        'Languages' => 'language',
        'Marketing' => 'megaphone',
        'Music & Audio' => 'musical-note',
        'Photography' => 'camera',
        'Programming' => 'code-bracket',
        'Video & Animation' => 'film',
        'Writing & Translation' => 'pencil-square',
        default => 'tag',
    };
@endphp

<flux:icon :name="$icon" :variant="$variant" aria-hidden="true" {{ $attributes->merge(['class' => 'size-3.5 shrink-0']) }} />
