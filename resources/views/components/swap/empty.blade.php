@props(['icon' => 'inbox', 'title'])

<x-swap.card padding="lg" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-3 py-14 text-center sm:py-20']) }}>
    <span class="relative mb-2 flex size-16 items-center justify-center rounded-2xl bg-brand-gradient text-paper shadow-lg shadow-navy/20">
        <flux:icon :name="$icon" variant="outline" class="size-7" aria-hidden="true" />
        <span class="absolute -top-1 -right-1 size-3 rounded-full bg-swap ring-4 ring-surface" aria-hidden="true"></span>
    </span>
    <h2 class="text-lg font-semibold text-strong">{{ $title }}</h2>
    <p class="max-w-sm text-sm leading-6 text-muted">{{ $slot }}</p>
    @isset($actions)
        <div class="mt-4">{{ $actions }}</div>
    @endisset
</x-swap.card>
