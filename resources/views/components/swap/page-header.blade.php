@props(['title', 'eyebrow' => null, 'level' => 1])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-2']) }}>
    @if ($eyebrow)
        <span class="eyebrow">{{ $eyebrow }}</span>
    @endif
    <h{{ $level }} class="text-2xl leading-tight font-semibold tracking-tight text-strong sm:text-3xl">{{ $title }}</h{{ $level }}>
    @if ($slot->isNotEmpty())
        <p class="max-w-2xl text-base leading-7 text-muted">{{ $slot }}</p>
    @endif
</div>
