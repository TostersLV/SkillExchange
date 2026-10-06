@props(['href' => '#', 'sidebar' => false, 'inverse' => false])

<a href="{{ $href }}" aria-label="SkillExchange home"
    class="flex items-center gap-2.5 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-4 {{ $sidebar ? '' : 'pe-2' }}" wire:navigate>
    {{-- Two arrows chasing each other round a circle: one skill goes out, another comes back. --}}
    <svg viewBox="0 0 34 34" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
        class="size-9 shrink-0">
        <rect width="34" height="34" rx="10" class="{{ $inverse ? 'fill-paper' : 'fill-navy dark:fill-paper' }}" />
        <path d="M9 15.5a8 8 0 0 1 14.2-4.6M23.6 6.8v4.6H19" class="stroke-swap" />
        <path d="M25 18.5a8 8 0 0 1-14.2 4.6M10.4 27.2v-4.6H15" class="{{ $inverse ? 'stroke-navy' : 'stroke-white dark:stroke-navy' }}" />
    </svg>

    <span class="text-lg leading-none font-extrabold tracking-tight {{ $inverse ? 'text-paper' : 'text-strong' }}">SkillExchange</span>
</a>
