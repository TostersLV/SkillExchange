@props(['icon' => null, 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-brand/6 text-strong ring-1 ring-inset ring-line',
        'strong' => 'bg-brand/10 text-strong ring-1 ring-inset ring-line',
        'swap' => 'bg-swap-soft text-swap-text ring-1 ring-inset ring-swap/25',
        'amber' => 'bg-amber/15 text-strong ring-1 ring-inset ring-amber/30 dark:bg-amber/20',
    ];

    $classes = 'inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium '
        .($tones[$tone] ?? $tones['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <flux:icon :name="$icon" variant="micro" class="size-3.5 opacity-80" aria-hidden="true" />
    @endif
    {{ $slot }}
</span>
