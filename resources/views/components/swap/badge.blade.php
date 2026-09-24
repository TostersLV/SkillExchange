@props(['icon' => null, 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-brand/8 text-strong',
        'strong' => 'bg-brand/10 text-strong',
        'amber' => 'bg-amber/15 text-strong dark:bg-amber/20',
    ];

    $classes = 'inline-flex shrink-0 items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium '
        .($tones[$tone] ?? $tones['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <flux:icon :name="$icon" variant="micro" class="size-3.5 opacity-80" aria-hidden="true" />
    @endif
    {{ $slot }}
</span>
