@props(['icon' => 'inbox', 'title'])

<x-swap.card padding="lg" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-3 py-12 text-center sm:py-16']) }}>
    <span class="flex size-12 items-center justify-center rounded-full bg-brand/6 text-strong">
        <flux:icon :name="$icon" variant="outline" class="size-6" aria-hidden="true" />
    </span>
    <h2 class="text-base font-semibold text-strong">{{ $title }}</h2>
    <p class="max-w-sm text-sm leading-6 text-muted">{{ $slot }}</p>
    @isset($actions)
        <div class="mt-3">{{ $actions }}</div>
    @endisset
</x-swap.card>
