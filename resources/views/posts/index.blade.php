<x-layouts::app title="Home">
    @php
        $user = auth()->user();
        $greeting = match (true) {
            now()->hour < 12 => 'Good morning',
            now()->hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    @endphp

    <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <flux:callout class="mt-8" icon="information-circle" heading="{{ session('status') }}" />
        @endif

        @if ($awaitingReview->isNotEmpty())
            <flux:callout class="mt-8" icon="star"
                heading="{{ $awaitingReview->count() === 1 ? 'You have an exchange to review' : 'You have ' . $awaitingReview->count() . ' exchanges to review' }}">
                <x-slot name="actions">
                    <x-swap.button size="sm"
                        href="{{ $awaitingReview->count() === 1 ? route('posts.progress.show', $awaitingReview->first()) : route('posts.progress') }}">
                        Review now
                    </x-swap.button>
                </x-slot>
            </flux:callout>
        @endif

        {{-- Personal overview: greeting on the left, the "wallet" of skills on the right. --}}
        <section class="flex flex-col gap-10 py-12 lg:flex-row lg:items-center lg:gap-12 lg:py-14">
            <div class="flex-1 space-y-4">
                <h1 class="text-4xl leading-[1.05] font-extrabold tracking-[-0.035em] text-strong sm:text-5xl">
                    {{ $greeting }}, {{ $user->username }}
                </h1>
                <p class="max-w-xl text-lg leading-relaxed text-muted">
                    You have {{ $pendingOffersCount }} pending {{ str('offer')->plural($pendingOffersCount) }}.
                    Here are new exchanges that match your skills.
                </p>
                <div class="flex flex-wrap gap-2.5 pt-2">
                    <x-swap.button href="#explore" size="lg" class="!rounded-xl">Browse exchanges</x-swap.button>
                    <x-swap.button href="{{ route('posts.offers') }}" variant="secondary" size="lg" class="!rounded-xl">
                        View offers
                    </x-swap.button>
                </div>
            </div>

            <aside aria-label="Your exchange profile"
                class="w-full shrink-0 space-y-5 rounded-[1.25rem] bg-navy p-6 text-paper shadow-[0_20px_40px_-16px_color-mix(in_oklab,#0f1e3d_45%,transparent)] ring-1 ring-paper/5 lg:w-[420px]">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-paper/70">Your exchange profile</p>
                </div>

                <div class="space-y-1">
                    <p class="text-xs font-semibold tracking-[0.08em] text-paper/70 uppercase">Reputation</p>
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        @if ($user->reputation !== null)
                            <span class="text-5xl leading-none font-extrabold tracking-tight tabular-nums">{{ number_format((float) $user->reputation, 1) }}</span>
                            <x-star-rating :rating="(float) $user->reputation" :show-score="false" empty-class="text-paper/20" />
                        @else
                            <span class="text-5xl leading-none font-extrabold tracking-tight">New</span>
                        @endif
                        <span class="text-sm text-paper/70">{{ $reviewsCount }} {{ str('review')->plural($reviewsCount) }}</span>
                    </div>
                </div>

                <dl class="grid grid-cols-3 gap-px overflow-hidden rounded-2xl bg-paper/10">
                    @foreach ([
                        ['label' => 'Active exchanges', 'value' => $activeExchangesCount, 'href' => route('posts.progress'), 'accent' => false],
                        ['label' => 'Offers waiting', 'value' => $pendingOffersCount, 'href' => route('posts.offers'), 'accent' => false],
                        ['label' => 'Completed swaps', 'value' => $completedExchangesCount, 'href' => route('posts.progress'), 'accent' => true],
                    ] as $stat)
                        <a href="{{ $stat['href'] }}" wire:navigate
                            class="flex flex-col-reverse gap-1 bg-[#15274b] p-3.5 transition-colors hover:bg-[#1b3159] focus-visible:outline-paper">
                            <dt class="text-xs text-paper/70">{{ $stat['label'] }}</dt>
                            <dd class="text-2xl font-extrabold tabular-nums {{ $stat['accent'] ? 'text-emerald-300' : '' }}">{{ $stat['value'] }}</dd>
                        </a>
                    @endforeach
                </dl>

                <div class="flex items-center justify-between gap-3 border-t border-paper/10 pt-4 text-sm">
                    <p class="min-w-0 truncate text-paper/70">
                        @if ($taughtSkills->isNotEmpty())
                            You teach: {{ $taughtSkills->implode(' · ') }}
                        @else
                            You have not shared a skill yet
                        @endif
                    </p>
                    <a href="{{ route('profile.show', $user) }}" wire:navigate
                        class="shrink-0 rounded font-bold text-emerald-300 hover:text-emerald-200 focus-visible:outline-paper">View profile →</a>
                </div>
            </aside>
        </section>
    </div>

    {{-- Live activity, styled like a market ticker. The items are rendered twice so the loop is seamless. --}}
    @if ($activity !== [])
        <div class="border-y border-line bg-surface" aria-label="Recent activity" role="region">
            <div class="mx-auto flex h-12 w-full max-w-[1200px] items-center gap-4 px-4 sm:px-6 lg:px-8">
                <span class="flex shrink-0 items-center gap-2 text-xs font-extrabold tracking-[0.1em] text-swap-text uppercase">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-swap opacity-60"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-swap"></span>
                    </span>
                    Live
                </span>

                <div class="mask-fade-x flex-1 overflow-hidden">
                    <div class="flex w-max animate-ticker hover:[animation-play-state:paused]">
                        @foreach ([false, true] as $isCopy)
                            <ul class="flex shrink-0 gap-10 pr-10 text-sm whitespace-nowrap text-muted" @if ($isCopy) aria-hidden="true" @endif>
                                @foreach ($activity as $item)
                                    <li class="flex items-center gap-2">
                                        <span class="size-1.5 rounded-full bg-swap" aria-hidden="true"></span>
                                        <strong class="font-semibold text-strong">{{ $item['lead'] }}</strong>
                                        {{ $item['text'] }}
                                        @if ($item['time'])
                                            <span class="text-muted/80">· {{ $item['time'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-8">
        <section id="explore" class="scroll-mt-24 pt-14">
            <x-swap.page-header title="Explore exchanges" :level="2" class="mb-6">
                Tell us what you want to learn and what you can teach. We'll find the swap.
            </x-swap.page-header>

            <livewire:posts.search-and-filter />
        </section>

        <section aria-label="Why SkillExchange" class="grid gap-4 pt-20 md:grid-cols-3">
            @foreach ([
                ['icon' => 'shield-check', 'tone' => 'bg-swap-soft text-swap-text', 'title' => 'Private exchange chats', 'text' => 'Only the two members of an exchange can read its chat.'],
                ['icon' => 'star', 'tone' => 'bg-amber/15 text-[#b45309] dark:text-amber', 'title' => 'Rated after every exchange', 'text' => 'Both sides leave a review when a swap is done, so reputation is earned.'],
                ['icon' => 'clock', 'tone' => 'bg-brand/8 text-strong', 'title' => 'Fair time-for-time swaps', 'text' => 'An hour of your skill for an hour of theirs. No money changes hands.'],
            ] as $point)
                <x-swap.card class="space-y-3">
                    <span class="flex size-11 items-center justify-center rounded-xl {{ $point['tone'] }}">
                        <flux:icon :name="$point['icon']" variant="outline" class="size-5" aria-hidden="true" />
                    </span>
                    <h3 class="text-lg font-bold tracking-tight text-strong">{{ $point['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-muted">{{ $point['text'] }}</p>
                </x-swap.card>
            @endforeach
        </section>
    </div>
</x-layouts::app>
