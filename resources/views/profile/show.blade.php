<x-layouts::app :title="$user->username">
    @php($isMe = $user->is(auth()->user()))

    <div class="mx-auto w-full max-w-[1200px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        {{-- Exchange profile: who they are on the left, their "balance" of trust on the right. --}}
        <section aria-label="Exchange profile"
            class="grid gap-8 rounded-[1.25rem] bg-navy p-6 text-paper shadow-[0_20px_40px_-16px_color-mix(in_oklab,#0f1e3d_45%,transparent)] ring-1 ring-paper/5 sm:p-8 lg:grid-cols-[1fr_420px] lg:items-center">
            <div class="flex min-w-0 items-center gap-5">
                <flux:avatar size="xl" circle :name="$user->username" :initials="$user->initials()" class="!bg-paper/10 !text-paper" />

                <div class="min-w-0 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="truncate text-3xl font-extrabold tracking-[-0.03em] sm:text-4xl">{{ $user->username }}</h1>
                    </div>

                    @if ($user->bio)
                        <p class="max-w-xl leading-7 whitespace-pre-line text-paper/75">{{ $user->bio }}</p>
                    @endif

                    <p class="text-sm text-paper/60">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <dl class="grid grid-cols-3 gap-px overflow-hidden rounded-2xl bg-paper/10">
                    <div class="flex flex-col-reverse gap-1 bg-[#15274b] p-4">
                        <dt class="text-xs text-paper/70">Reputation</dt>
                        <dd class="flex items-center gap-1.5 text-2xl font-extrabold tabular-nums">
                            @if ($user->reputation !== null)
                                {{ number_format((float) $user->reputation, 1) }}
                                <flux:icon.star variant="micro" class="size-4 text-amber" aria-hidden="true" />
                            @else
                                New
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col-reverse gap-1 bg-[#15274b] p-4">
                        <dt class="text-xs text-paper/70">{{ str('Review')->plural($reviewsCount) }}</dt>
                        <dd class="text-2xl font-extrabold tabular-nums">{{ $reviewsCount }}</dd>
                    </div>
                    <div class="flex flex-col-reverse gap-1 bg-[#15274b] p-4">
                        <dt class="text-xs text-paper/70">Completed swaps</dt>
                        <dd class="text-2xl font-extrabold text-emerald-300 tabular-nums">{{ $completedExchangesCount }}</dd>
                    </div>
                </dl>

                @if ($isMe)
                    <x-swap.button href="{{ route('profile.edit') }}" variant="inverse" class="w-full">
                        <flux:icon.pencil-square variant="micro" />
                        Edit profile
                    </x-swap.button>
                @endif
            </div>
        </section>

        <div class="mt-14 mb-6 flex flex-wrap items-end justify-between gap-4">
            <h2 class="text-2xl font-extrabold tracking-[-0.02em] text-strong">
                {{ $isMe ? 'Your exchanges' : $user->username.'\'s open exchanges' }}
            </h2>
            @if ($isMe)
                <x-swap.button href="{{ route('posts.create') }}" variant="swap" size="sm">
                    <flux:icon.plus variant="micro" />
                    Post an exchange
                </x-swap.button>
            @endif
        </div>

        <x-posts.results :posts="$posts" :closed-offers="$closedOffers"
            :empty-title="$isMe ? 'You have not posted yet' : 'No open exchanges'"
            :empty-text="$isMe ? 'Post an exchange to start trading with the community.' : $user->username.' has no open exchanges right now.'"
            :show-empty-action="$isMe" />
    </div>
</x-layouts::app>
