@props(['title', 'eyebrow' => null, 'level' => 1])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
    @if ($eyebrow)
        <span class="inline-flex w-fit items-center gap-1.5 rounded-full border border-line bg-surface px-3 py-1 text-xs font-semibold text-muted">
            <span class="size-1.5 rounded-full bg-swap" aria-hidden="true"></span>
            {{ $eyebrow }}
        </span>
    @endif
    <h{{ $level }} class="text-3xl leading-[1.1] font-extrabold tracking-[-0.03em] text-strong sm:text-4xl">{{ $title }}</h{{ $level }}>
    @if ($slot->isNotEmpty())
        <p class="max-w-2xl text-base leading-7 text-muted sm:text-lg">{{ $slot }}</p>
    @endif
</div>
