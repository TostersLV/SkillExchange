@props(['status'])

@php
    // Differentiated by icon plus a subtle tint; amber is reserved for things still in motion.
    [$icon, $tone] = match ($status) {
        \App\PostStatus::AVAILABLE => ['sparkles', 'neutral'],
        \App\PostStatus::IN_PROGRESS => ['clock', 'amber'],
        \App\PostStatus::COMPLETED => ['check', 'strong'],
        \App\PostStatus::CANCELLED => ['x-mark', 'neutral'],
        \App\PostOfferStatus::PENDING => ['clock', 'amber'],
        \App\PostOfferStatus::ACCEPTED => ['check', 'strong'],
        \App\PostOfferStatus::REJECTED => ['x-mark', 'neutral'],
        default => [null, 'neutral'],
    };
@endphp

<x-swap.badge :icon="$icon" :tone="$tone" {{ $attributes }}>
    {{ $status->label() }}
</x-swap.badge>
