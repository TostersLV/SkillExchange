<x-layouts::app :title="$user->username">
    <div class="mx-auto w-full max-w-[1200px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <x-swap.card padding="lg" class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <flux:avatar size="xl" :name="$user->username" :initials="$user->initials()" />

                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-semibold tracking-tight text-strong sm:text-3xl">{{ $user->username }}</h1>

                    @if ($user->bio)
                        <p class="mt-1.5 max-w-xl leading-7 whitespace-pre-line text-muted">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-1.5 sm:items-end">
                <p class="eyebrow">Reputation</p>

                @if ($user->reputation !== null)
                    <x-star-rating :rating="$user->reputation" />
                @else
                    <p class="text-sm text-muted">No reviews yet</p>
                @endif
            </div>
        </x-swap.card>

        <h2 class="mt-12 mb-6 text-xl font-semibold tracking-tight text-strong">
            {{ $user->is(auth()->user()) ? 'Your posts' : $user->username.'\'s posts' }}
        </h2>

        <x-posts.results :posts="$posts"
            :empty-title="$user->is(auth()->user()) ? 'You have not posted yet' : 'No posts yet'"
            :empty-text="$user->is(auth()->user()) ? 'Share a skill to start trading with the community.' : $user->username.' has not shared any skills yet.'"
            :show-empty-action="$user->is(auth()->user())" />
    </div>
</x-layouts::app>
