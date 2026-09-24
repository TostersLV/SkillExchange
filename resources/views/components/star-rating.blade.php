@props(['rating', 'max' => 5, 'showScore' => true])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1.5']) }}>
    <div class="flex items-center gap-0.5" role="img" aria-label="Rated {{ number_format($rating, 1) }} out of {{ $max }}">
        @for ($i = 1; $i <= $max; $i++)
            <flux:icon.star variant="micro" class="size-4 {{ $i <= round($rating) ? 'text-amber' : 'text-brand/15' }}" />
        @endfor
    </div>

    @if ($showScore)
        <span class="text-sm font-semibold text-strong tabular-nums" aria-hidden="true">{{ number_format($rating, 1) }}</span>
    @endif
</div>
