@props(['offering', 'looking', 'active' => true, 'size' => 'md', 'stacked' => false, 'headingLevel' => null])

{{--
    The SkillExchange trade visual: an OFFERS pill and a WANTS pill joined by a swap arrow.
    "stacked" lays the pills out vertically (narrow sidebars); otherwise they stack only on phones.
    "headingLevel" renders the offered skill as that heading level, for pages where the trade is the title.
--}}
@php
    $isLarge = $size === 'lg';
    $pill = $isLarge ? 'rounded-2xl px-5 py-4' : 'rounded-full px-4 py-2.5'.($stacked ? ' !rounded-2xl' : ' max-sm:rounded-2xl');
    $skill = $isLarge ? 'text-xl sm:text-2xl leading-tight font-extrabold tracking-tight break-words' : 'truncate font-bold';
    $offerTag = $headingLevel ? 'h'.$headingLevel : 'p';
@endphp

<div {{ $attributes->class(['flex items-center', 'flex-col items-stretch' => $stacked, 'max-sm:flex-col max-sm:items-stretch' => ! $stacked]) }}>
    <div class="min-w-0 flex-1 bg-raised {{ $pill }}">
        <p class="text-[10px] font-extrabold tracking-[0.12em] text-muted">OFFERS</p>
        <{{ $offerTag }} class="{{ $skill }} text-strong">{{ $offering }}</{{ $offerTag }}>
    </div>

    <div @class([
        'relative flex shrink-0 items-center justify-center self-center',
        $isLarge ? 'w-20' : 'w-14',
        'h-10 rotate-90' => $stacked,
        'max-sm:h-10 max-sm:rotate-90' => ! $stacked,
    ])>
        <span @class(['absolute inset-x-0 top-1/2 h-0.5 -translate-y-1/2', 'bg-swap/30' => $active, 'bg-line' => ! $active])></span>
        <span @class([
            'relative flex items-center justify-center rounded-full text-white transition-transform duration-300 group-hover:rotate-180',
            $isLarge ? 'size-12 shadow-lg shadow-swap/30' : 'size-8',
            'bg-swap' => $active,
            'bg-muted/50' => ! $active,
        ])>
            <x-swap.icon :class="$isLarge ? 'size-5' : 'size-4'" />
            <span class="sr-only">in exchange for</span>
        </span>
    </div>

    <div @class(['min-w-0 flex-1', $pill, 'bg-swap-soft' => $active, 'bg-raised' => ! $active])>
        <p @class(['text-[10px] font-extrabold tracking-[0.12em]', 'text-swap-text' => $active, 'text-muted' => ! $active])>WANTS</p>
        <p class="{{ $skill }} text-strong">{{ $looking }}</p>
    </div>
</div>
