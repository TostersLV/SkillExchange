<x-layouts::app title="Home">
    <section class="mx-auto w-full max-w-[1200px] px-4 pt-12 pb-16 sm:px-6 lg:px-8 lg:pt-20 lg:pb-24">
        <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="motion-safe:animate-fade-in">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full border border-amber/50 bg-amber/10 px-3 py-1 text-xs font-semibold text-strong">
                    <flux:icon.banknotes variant="micro" class="size-3.5 text-amber" aria-hidden="true" />
                    No money involved
                </span>

                <h1 class="mt-6 max-w-xl text-4xl leading-[1.1] font-semibold tracking-tight text-strong sm:text-5xl lg:text-[3.5rem]">
                    Trade what you know for what you need
                </h1>

                <p class="mt-5 max-w-lg text-lg leading-8 text-muted">
                    Offer a skill you have, learn one you want. Every exchange is free: you pay with your time and
                    knowledge, never money.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-swap.button href="{{ route('posts.create') }}" size="lg">
                        <flux:icon.plus variant="micro" />
                        Share a skill
                    </x-swap.button>
                    <x-swap.button href="#listings" variant="secondary" size="lg">
                        Browse offers
                    </x-swap.button>
                </div>
            </div>

            {{-- Two people exchanging: a simple line illustration, no invented data. --}}
            <div class="hidden lg:block" aria-hidden="true">
                <svg viewBox="0 0 440 320" fill="none" class="w-full text-strong">
                    <rect x="8" y="8" width="424" height="304" rx="24" fill="var(--color-surface)"
                        stroke="var(--color-line-strong)" stroke-width="1" />

                    <circle cx="112" cy="116" r="28" stroke="currentColor" stroke-width="2" />
                    <path d="M56 232c0-31 25-56 56-56s56 25 56 56" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

                    <circle cx="328" cy="116" r="28" stroke="currentColor" stroke-width="2" />
                    <path d="M272 232c0-31 25-56 56-56s56 25 56 56" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

                    <rect x="170" y="80" width="100" height="36" rx="8" fill="var(--color-page)" stroke="currentColor" stroke-opacity="0.2" />
                    <path d="M188 98h58" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-opacity="0.5" />
                    <path d="m240 90 10 8-10 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <rect x="170" y="140" width="100" height="36" rx="8" fill="var(--color-page)" stroke="currentColor" stroke-opacity="0.2" />
                    <path d="M252 158h-58" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-opacity="0.5" />
                    <path d="m200 150-10 8 10 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <path d="m220 208 5.3 10.8 11.9 1.7-8.6 8.4 2 11.8-10.6-5.6-10.6 5.6 2-11.8-8.6-8.4 11.9-1.7z"
                        fill="var(--color-amber)" />
                    <path d="M40 272h360" stroke="currentColor" stroke-opacity="0.12" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </section>

    <section aria-labelledby="how-it-works" class="bg-navy text-paper dark:border-y dark:border-line dark:bg-band">
        <div class="mx-auto w-full max-w-[1200px] px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold tracking-[0.08em] text-paper/70 uppercase">How it works</p>
                <h2 id="how-it-works" class="mt-2 text-2xl font-semibold tracking-tight text-paper sm:text-3xl">
                    Three steps to your first exchange
                </h2>
            </div>

            <ol class="mt-10 grid gap-8 md:grid-cols-3 md:gap-10">
                @foreach ([
                    ['icon' => 'pencil-square', 'title' => 'Post your skill', 'body' => 'Describe what you can teach and what you would like to learn in return.'],
                    ['icon' => 'user-group', 'title' => 'Match with someone', 'body' => 'Browse offers or wait for someone to reach out, then accept the match that fits.'],
                    ['icon' => 'star', 'title' => 'Exchange & leave a review', 'body' => 'Meet, trade skills, then rate each other so the next person knows who to trust.'],
                ] as $step)
                    <li class="flex gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-paper/20 text-paper">
                            <flux:icon :name="$step['icon']" variant="outline" class="size-5" aria-hidden="true" />
                        </span>
                        <div>
                            <p class="text-sm font-medium text-paper/60">Step {{ $loop->iteration }}</p>
                            <h3 class="mt-0.5 text-base font-semibold text-paper">{{ $step['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-paper/75">{{ $step['body'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-12 flex items-start gap-3 rounded-xl border border-paper/15 bg-paper/5 p-5 sm:items-center">
                <flux:icon.shield-check variant="outline" class="size-6 shrink-0 text-amber" aria-hidden="true" />
                <p class="text-sm leading-6 text-paper/85">
                    <span class="font-semibold text-paper">Safe exchanges.</span>
                    After every completed swap both people leave a rating, so each member builds a visible reputation
                    over time. Check someone's profile and reviews before you accept.
                </p>
            </div>
        </div>
    </section>

    <section id="listings" class="mx-auto w-full max-w-[1200px] scroll-mt-20 px-4 pt-16 sm:px-6 lg:px-8 lg:pt-20">
        @if (session('status'))
            <flux:callout class="mb-6" icon="information-circle" heading="{{ session('status') }}" />
        @endif

        @if ($awaitingReview->isNotEmpty())
            <flux:callout class="mb-8" icon="star"
                heading="{{ $awaitingReview->count() === 1 ? 'You have an exchange to review' : 'You have ' . $awaitingReview->count() . ' exchanges to review' }}">
                <x-slot name="actions">
                    <x-swap.button size="sm"
                        href="{{ $awaitingReview->count() === 1 ? route('posts.progress.show', $awaitingReview->first()) : route('posts.progress') }}">
                        Review now
                    </x-swap.button>
                </x-slot>
            </flux:callout>
        @endif

        <x-swap.page-header title="Browse offers" eyebrow="Open exchanges" :level="2" class="mb-8">
            Find someone who can teach what you want to learn.
        </x-swap.page-header>

        <livewire:posts.search-and-filter />
    </section>
</x-layouts::app>
