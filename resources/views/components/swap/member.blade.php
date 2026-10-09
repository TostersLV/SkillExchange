@props(['user', 'link' => false, 'caption' => null])

{{-- A member's avatar, name and rating. "caption" replaces the rating line (e.g. "Sent 2 days ago"). --}}
<div {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-3']) }}>
    <flux:avatar size="lg" circle :name="$user->username" :initials="$user->initials()" />

    <div class="min-w-0 space-y-0.5">
        <p class="flex items-center gap-1.5">
            @if ($link)
                <a href="{{ route('profile.show', $user) }}" wire:navigate
                    class="truncate rounded font-bold text-strong underline-offset-2 hover:underline">{{ $user->username }}</a>
            @else
                <span class="truncate font-bold text-strong">{{ $user->username }}</span>
            @endif
        </p>

        @if ($caption)
            <p class="text-sm text-muted">{{ $caption }}</p>
        @elseif ($user->reputation !== null)
            <p class="flex items-center gap-1 text-sm text-muted">
                <flux:icon.star variant="micro" class="size-3.5 text-amber" aria-hidden="true" />
                <span class="font-bold text-strong tabular-nums">{{ number_format((float) $user->reputation, 1) }}</span>
                <span class="sr-only">out of 5</span>
            </p>
        @else
            <p class="text-sm text-muted">No reviews yet</p>
        @endif
    </div>
</div>
