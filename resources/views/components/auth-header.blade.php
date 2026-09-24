@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5 text-center">
    <h1 class="text-2xl font-semibold tracking-tight text-strong">{{ $title }}</h1>
    <p class="text-sm leading-6 text-muted">{{ $description }}</p>
</div>
