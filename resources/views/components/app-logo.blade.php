@props(['href' => '#', 'sidebar' => false, 'inverse' => false])

<a href="{{ $href }}" aria-label="SkillExchange home"
    class="flex items-center gap-2.5 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 {{ $sidebar ? '' : 'pe-2' }}" wire:navigate>
    <span
        class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $inverse ? 'bg-paper text-navy' : 'bg-brand text-on-brand' }}">
        <x-swap.icon class="size-[18px]" />
    </span>

    <span class="text-[15px] leading-none font-semibold tracking-tight {{ $inverse ? 'text-paper' : 'text-strong' }}">
        SkillExchange
    </span>
</a>
