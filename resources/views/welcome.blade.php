<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => request()->cookie('appearance') === 'dark'])>
    <head>
        @include('partials.head', ['title' => 'Trade skills, not money'])
    </head>
    <body class="flex min-h-screen flex-col bg-page font-sans text-fg antialiased">
        <header class="bg-navy text-paper dark:bg-band">
            <div class="mx-auto flex h-16 w-full max-w-[1200px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <x-app-logo href="{{ route('welcome') }}" inverse />

                <nav aria-label="Account" class="flex items-center gap-2">
                    <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" size="sm" square
                        class="!text-paper/80 hover:!bg-paper/10 hover:!text-paper" aria-label="Toggle dark mode">
                        <flux:icon.moon variant="mini" class="dark:hidden" />
                        <flux:icon.sun variant="mini" class="hidden dark:block" />
                    </flux:button>
                    <x-swap.button href="{{ route('login') }}" variant="outline-inverse" size="sm" class="max-sm:hidden">Log in</x-swap.button>
                    <x-swap.button href="{{ route('register') }}" variant="inverse" size="sm">Sign up</x-swap.button>
                </nav>
            </div>
        </header>

        <main id="main" class="flex-1">
        {{-- Hero (navy, continues the header) --}}
        <div class="relative isolate overflow-hidden bg-navy text-paper dark:bg-band">
            {{-- Soft dot grid + glow for depth --}}
            <div aria-hidden="true"
                class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(color-mix(in_oklab,var(--color-paper)_12%,transparent)_1px,transparent_1px)] [background-size:24px_24px] [background-position:12px_12px] [mask-image:radial-gradient(ellipse_at_center,black_30%,transparent_75%)]">
            </div>
            <div aria-hidden="true"
                class="pointer-events-none absolute top-24 left-1/2 -z-10 size-[32rem] -translate-x-1/2 rounded-full bg-swap/10 blur-3xl">
            </div>


                <section class="mx-auto w-full max-w-[1200px] px-4 pt-16 pb-20 text-center sm:px-6 lg:px-8 lg:pt-28 lg:pb-28">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-paper/20 bg-paper/5 px-3 py-1 text-xs font-semibold text-paper">
                        <flux:icon.banknotes variant="micro" class="size-3.5 text-swap" aria-hidden="true" />
                        No money involved, ever
                    </span>

                    <h1
                        class="mx-auto mt-6 max-w-3xl text-4xl leading-[1.05] font-extrabold tracking-[-0.035em] text-paper sm:text-6xl lg:text-7xl">
                        Trade what you know for what you
                        <span class="text-swap">need</span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-paper/75">
                        Teach guitar, learn Spanish. Fix a website, learn to bake. SkillExchange connects people who
                        want to learn from each other, and you pay with your time, never money.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                        <x-swap.button href="{{ route('register') }}" variant="swap" size="lg">
                            Get started for free
                            <flux:icon.arrow-right variant="micro" />
                        </x-swap.button>
                        <x-swap.button href="{{ route('login') }}" variant="outline-inverse" size="lg">
                            I already have an account
                        </x-swap.button>
                    </div>

                    {{-- Example swaps (illustrative, not real listings) --}}
                    <div class="mt-16">
                        <p class="text-xs font-semibold tracking-[0.08em] text-paper/60 uppercase">Example swaps</p>
                        <ul class="mx-auto mt-4 flex max-w-3xl flex-wrap justify-center gap-3">
                            @foreach ([['Guitar', 'Spanish'], ['Web design', 'Baking'], ['Yoga', 'Photography'], ['Coding', 'Cooking'], ['Math tutoring', 'Car repair']] as [$offering, $lookingFor])
                                <li
                                    class="inline-flex items-center gap-2 rounded-full border border-paper/15 bg-paper/5 px-4 py-2 text-sm font-medium text-paper backdrop-blur-sm">
                                    {{ $offering }}
                                    <x-swap.icon class="size-4 text-swap" />
                                    <span class="sr-only">for</span>
                                    {{ $lookingFor }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
        </div>

        <x-swap.how-it-works />

        {{-- Why SkillExchange --}}
        <section aria-labelledby="why" class="border-y border-line bg-surface">
            <div class="mx-auto grid w-full max-w-[1200px] gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:py-28">
                <div>
                    <p class="eyebrow">Why SkillExchange</p>
                    <h2 id="why" class="mt-2 text-3xl font-extrabold tracking-[-0.03em] text-strong sm:text-4xl">
                        Learning shouldn't depend on your budget
                    </h2>
                    <p class="mt-4 max-w-md text-lg leading-8 text-muted">
                        Everyone is good at something. SkillExchange turns what you already know into the currency
                        for what you want to learn next.
                    </p>
                </div>

                <dl class="grid gap-8 sm:grid-cols-2">
                    @foreach ([
                        ['icon' => 'banknotes', 'title' => 'Completely free', 'body' => 'No fees, no subscriptions, no payments between members. Skills are the only currency.'],
                        ['icon' => 'star', 'title' => 'Reputation you can see', 'body' => 'Both people rate each exchange, so you can check someone\'s reviews before you agree to meet.'],
                        ['icon' => 'chat-bubble-left-right', 'title' => 'Plan it together', 'body' => 'Once an offer is accepted you get a private chat to agree on when, where and how to swap.'],
                        ['icon' => 'squares-2x2', 'title' => 'Every kind of skill', 'body' => 'From languages and music to coding, design and DIY. Search and filter by category to find a match.'],
                    ] as $feature)
                        <div>
                            <dt class="flex items-center gap-3 text-base font-semibold text-strong">
                                <span class="flex size-9 items-center justify-center rounded-lg bg-swap-soft text-swap-text">
                                    <flux:icon :name="$feature['icon']" variant="outline" class="size-5" aria-hidden="true" />
                                </span>
                                {{ $feature['title'] }}
                            </dt>
                            <dd class="mt-2 text-sm leading-6 text-muted">{{ $feature['body'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- FAQ --}}
        <section aria-labelledby="faq" class="mx-auto w-full max-w-3xl px-4 py-20 sm:px-6 lg:py-28">
            <div class="text-center">
                <p class="eyebrow">Questions</p>
                <h2 id="faq" class="mt-2 text-3xl font-extrabold tracking-[-0.03em] text-strong sm:text-4xl">Good to know</h2>
            </div>

            <div class="mt-10 divide-y divide-line rounded-xl border border-line bg-surface">
                @foreach ([
                    ['q' => 'Is it really free?', 'a' => 'Yes. There is no money involved at any point. You trade one skill for another, and your time is the only thing you give.'],
                    ['q' => 'How do I find someone to swap with?', 'a' => 'Browse posts and filter by category, then send an offer with a short message. You can also post your own skill and wait for offers.'],
                    ['q' => 'How do I know who to trust?', 'a' => 'After every completed exchange both people rate each other. Check a member\'s profile and reputation before you accept an offer.'],
                    ['q' => 'What happens after an offer is accepted?', 'a' => 'The exchange moves to In Progress, where you get a private chat to plan it. When you\'re both done, mark it complete and leave a review.'],
                ] as $item)
                    <details class="group px-5 py-4 sm:px-6">
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between gap-4 rounded font-medium text-strong [&::-webkit-details-marker]:hidden">
                            {{ $item['q'] }}
                            <flux:icon.plus variant="micro"
                                class="size-4 shrink-0 text-muted transition-transform duration-200 group-open:rotate-45" aria-hidden="true" />
                        </summary>
                        <p class="mt-3 text-sm leading-6 text-muted">{{ $item['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- Final call to action --}}
        <section class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-8">
            <div
                class="relative isolate overflow-hidden rounded-2xl bg-navy px-6 py-14 text-center text-paper sm:px-12 sm:py-16 dark:border dark:border-line dark:bg-band">
                <div aria-hidden="true"
                    class="pointer-events-none absolute -right-24 -bottom-24 -z-10 size-80 rounded-full bg-swap/15 blur-3xl"></div>
                <h2 class="text-3xl font-extrabold tracking-[-0.03em] text-paper sm:text-4xl">Ready for your first exchange?</h2>
                <p class="mx-auto mt-4 max-w-md text-paper/75">Create an account, post a skill, and see who wants to trade.</p>
                <div class="mt-8 flex justify-center">
                    <x-swap.button href="{{ route('register') }}" variant="swap" size="lg">
                        Create your free account
                        <flux:icon.arrow-right variant="micro" />
                    </x-swap.button>
                </div>
            </div>
        </section>

        </main>

        <footer class="mt-24 bg-band text-paper">
            <div
                class="mx-auto flex w-full max-w-[1200px] flex-col gap-6 px-4 py-12 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <div class="space-y-3">
                    <x-app-logo href="{{ route('welcome') }}" :sidebar="true" :inverse="true" />
                    <p class="text-sm text-paper/70">Trade skills, not money.</p>
                </div>
                <nav aria-label="Footer" class="flex gap-6 text-sm">
                    <a href="{{ route('login') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper">Log in</a>
                    <a href="{{ route('register') }}" class="rounded text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper">Sign up</a>
                </nav>
            </div>
            <div class="mx-auto w-full max-w-[1200px] border-t border-paper/10 px-4 py-6 text-sm text-paper/60 sm:px-6 lg:px-8">
                &copy; {{ now()->year }} SkillExchange
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
