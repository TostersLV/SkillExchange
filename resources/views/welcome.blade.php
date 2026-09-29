<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => request()->cookie('appearance') === 'dark'])>
    <head>
        @include('partials.head', ['title' => 'Trade skills, not money'])
    </head>
    <body class="flex min-h-screen flex-col bg-page font-sans text-fg antialiased">
        <header class="sticky top-0 z-10 border-b border-line bg-surface">
            <div class="mx-auto flex h-16 w-full max-w-[1200px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <x-app-logo href="{{ route('welcome') }}" />

                <nav aria-label="Account" class="flex items-center gap-2">
                    <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" size="sm" square
                        aria-label="Toggle dark mode">
                        <flux:icon.moon variant="mini" class="dark:hidden" />
                        <flux:icon.sun variant="mini" class="hidden dark:block" />
                    </flux:button>
                    <x-swap.button href="{{ route('login') }}" variant="ghost" size="sm">Log in</x-swap.button>
                    <x-swap.button href="{{ route('register') }}" size="sm">Sign up</x-swap.button>
                </nav>
            </div>
        </header>

        <main id="main" class="flex-1">
            {{-- Hero --}}
            <section class="mx-auto w-full max-w-[1200px] px-4 pt-14 pb-16 sm:px-6 lg:px-8 lg:pt-24 lg:pb-28">
                <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                    <div>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-amber/50 bg-amber/10 px-3 py-1 text-xs font-semibold text-strong">
                            <flux:icon.banknotes variant="micro" class="size-3.5 text-amber" aria-hidden="true" />
                            No money involved
                        </span>

                        <h1
                            class="mt-6 max-w-xl text-4xl leading-[1.1] font-semibold tracking-tight text-strong sm:text-5xl lg:text-[3.5rem]">
                            Trade what you know for what you need
                        </h1>

                        <p class="mt-5 max-w-lg text-lg leading-8 text-muted">
                            SkillExchange connects people who want to learn from each other. Teach guitar, learn
                            Spanish. Fix a website, learn to bake. Every exchange is free: you pay with your time and
                            knowledge, never money.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <x-swap.button href="{{ route('register') }}" size="lg">
                                Get started for free
                                <flux:icon.arrow-right variant="micro" />
                            </x-swap.button>
                            <x-swap.button href="{{ route('login') }}" variant="secondary" size="lg">
                                I already have an account
                            </x-swap.button>
                        </div>
                    </div>

                    <div class="hidden lg:block" aria-hidden="true">
                        <x-swap.illustration />
                    </div>
                </div>
            </section>

            <x-swap.how-it-works />

            {{-- Why SkillExchange --}}
            <section aria-labelledby="why" class="mx-auto w-full max-w-[1200px] px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <div class="max-w-2xl">
                    <p class="eyebrow">Why SkillExchange</p>
                    <h2 id="why" class="mt-2 text-2xl font-semibold tracking-tight text-strong sm:text-3xl">
                        Learning shouldn't depend on your budget
                    </h2>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ([
                        ['icon' => 'banknotes', 'title' => 'Completely free', 'body' => 'No fees, no subscriptions, no payments between members. Skills are the only currency.'],
                        ['icon' => 'star', 'title' => 'Reputation you can see', 'body' => 'Both people rate each exchange, so you can check someone\'s reviews before you agree to meet.'],
                        ['icon' => 'chat-bubble-left-right', 'title' => 'Plan it together', 'body' => 'Once an offer is accepted you get a private chat to agree on when, where and how to swap.'],
                    ] as $feature)
                        <x-swap.card>
                            <span class="flex size-10 items-center justify-center rounded-lg bg-brand/6 text-strong">
                                <flux:icon :name="$feature['icon']" variant="outline" class="size-5" aria-hidden="true" />
                            </span>
                            <h3 class="mt-4 text-base font-semibold text-strong">{{ $feature['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-muted">{{ $feature['body'] }}</p>
                        </x-swap.card>
                    @endforeach
                </div>
            </section>

            {{-- Final call to action --}}
            <section class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-8">
                <x-swap.card padding="lg"
                    class="flex flex-col items-start gap-6 sm:flex-row sm:items-center sm:justify-between sm:p-10">
                    <div>
                        <h2 class="text-2xl font-semibold tracking-tight text-strong">Ready for your first exchange?</h2>
                        <p class="mt-2 text-muted">Create an account, post a skill, and see who wants to trade.</p>
                    </div>
                    <x-swap.button href="{{ route('register') }}" size="lg" class="shrink-0">
                        Create your account
                    </x-swap.button>
                </x-swap.card>
            </section>
        </main>

        <footer class="mt-24 bg-navy text-paper dark:border-t dark:border-line dark:bg-band">
            <div
                class="mx-auto flex w-full max-w-[1200px] flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <div class="space-y-3">
                    <x-app-logo href="{{ route('welcome') }}" :sidebar="true" inverse />
                    <p class="text-sm text-paper/70">A community for trading skills. No money changes hands.</p>
                </div>

                <nav aria-label="Footer" class="flex gap-6 text-sm">
                    <a href="{{ route('login') }}" class="rounded text-paper/80 transition-colors hover:text-paper">Log in</a>
                    <a href="{{ route('register') }}" class="rounded text-paper/80 transition-colors hover:text-paper">Sign up</a>
                </nav>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
