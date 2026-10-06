<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => request()->cookie('appearance') === 'dark'])>

<head>
    @include('partials.head')
</head>

<body class="flex min-h-screen flex-col bg-page font-sans text-fg antialiased">
    <a href="#main"
        class="sr-only z-50 rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-on-brand focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
        Skip to content
    </a>

    @php($pendingOffersCount = auth()->user()->pendingReceivedOffersCount())

    <flux:header container class="sticky top-0 z-20 h-18 border-b border-line !bg-surface">
        <flux:sidebar.toggle class="mr-2 lg:hidden" icon="bars-2" inset="left" aria-label="Open menu" />

        <x-app-logo href="{{ route('home') }}" />

        <flux:spacer />

        {{-- In the normal flow between two spacers, so it stays centered without ever sliding under the actions on the right. --}}
        <flux:navbar class="flex shrink-0 items-center gap-1 !py-0 max-lg:hidden">
            <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>Explore
            </flux:navbar.item>
            <flux:navbar.item :href="route('posts.requests')" :current="request()->routeIs('posts.requests')"
                wire:navigate>Requests</flux:navbar.item>
            <flux:navbar.item :href="route('posts.offers')" :current="request()->routeIs('posts.offers')" wire:navigate>
                Offers</flux:navbar.item>
            <flux:navbar.item :href="route('posts.progress')" :current="request()->routeIs('posts.progress*')"
                wire:navigate>In Progress</flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <div class="flex shrink-0 items-center gap-1.5">
            <a href="{{ route('home') }}#explore" aria-label="Search exchanges"
                class="flex size-10 items-center justify-center rounded-xl border border-line text-strong transition-colors hover:bg-raised max-sm:hidden lg:max-xl:hidden">
                <flux:icon.magnifying-glass variant="mini" class="size-[18px]" />
            </a>

            <a href="{{ route('posts.offers') }}" wire:navigate
                aria-label="{{ $pendingOffersCount > 0 ? 'Offers, '.$pendingOffersCount.' waiting for you' : 'Offers' }}"
                class="relative flex size-10 items-center justify-center rounded-xl border border-line text-strong transition-colors hover:bg-raised">
                <flux:icon.bell variant="mini" class="size-[18px]" />
                @if ($pendingOffersCount > 0)
                    <span aria-hidden="true"
                        class="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-error px-1 text-[10px] font-bold text-white ring-2 ring-surface">
                        {{ $pendingOffersCount > 9 ? '9+' : $pendingOffersCount }}
                    </span>
                @endif
            </a>

            <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" square class="!size-10"
                aria-label="Toggle dark mode">
                <flux:icon.moon variant="mini" class="dark:hidden" />
                <flux:icon.sun variant="mini" class="hidden dark:block" />
            </flux:button>

            <x-swap.button variant="swap" href="{{ route('posts.create') }}" class="!h-10 !rounded-xl !px-4 max-md:hidden lg:max-xl:!px-3">
                <flux:icon.plus variant="micro" />
                <span class="lg:max-xl:sr-only">Post an exchange</span>
            </x-swap.button>

            <x-desktop-user-menu />
        </div>
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar collapsible="mobile" sticky class="border-e border-line !bg-surface lg:hidden">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('home') }}" />
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group heading="Menu">
                <flux:sidebar.item icon="magnifying-glass" :href="route('home')" :current="request()->routeIs('home')"
                    wire:navigate>Explore</flux:sidebar.item>
                <flux:sidebar.item icon="plus" :href="route('posts.create')"
                    :current="request()->routeIs('posts.create')" wire:navigate>Post an exchange</flux:sidebar.item>
                <flux:sidebar.item icon="inbox" :href="route('posts.requests')"
                    :current="request()->routeIs('posts.requests')" wire:navigate>Requests</flux:sidebar.item>
                <flux:sidebar.item icon="hand-raised" :href="route('posts.offers')"
                    :current="request()->routeIs('posts.offers')" wire:navigate>Offers</flux:sidebar.item>
                <flux:sidebar.item icon="arrow-path" :href="route('posts.progress')"
                    :current="request()->routeIs('posts.progress')" wire:navigate>In Progress</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />
    </flux:sidebar>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="mt-28 bg-band text-paper">
        <div class="mx-auto flex w-full max-w-[1200px] flex-col gap-12 px-4 pt-14 pb-10 sm:px-6 md:flex-row lg:px-8">
            <div class="space-y-4 md:w-80">
                <x-app-logo href="{{ route('home') }}" :sidebar="true" :inverse="true" />
                <p class="text-sm text-paper/70">Trade skills, not money.</p>
            </div>

            <nav aria-label="Footer" class="grid flex-1 grid-cols-2 gap-8 sm:grid-cols-3">
                @foreach ([
                    'Exchange' => ['Explore' => route('home'), 'Post an exchange' => route('posts.create'), 'Requests' => route('posts.requests')],
                    'Your swaps' => ['Offers' => route('posts.offers'), 'In Progress' => route('posts.progress')],
                    'Account' => ['Profile' => route('profile.show', auth()->user()), 'Settings' => route('profile.edit')],
                ] as $heading => $links)
                    <div class="flex flex-col gap-3">
                        <p class="text-xs font-extrabold tracking-[0.1em] text-swap uppercase">{{ $heading }}</p>
                        @foreach ($links as $label => $href)
                            <a href="{{ $href }}" wire:navigate
                                class="w-fit rounded text-sm text-paper/80 transition-colors hover:text-paper focus-visible:outline-paper">{{ $label }}</a>
                        @endforeach
                    </div>
                @endforeach
            </nav>
        </div>

        <div class="mx-auto w-full max-w-[1200px] border-t border-paper/10 px-4 py-6 text-sm text-paper/60 sm:px-6 lg:px-8">
            &copy; {{ now()->year }} SkillExchange
        </div>
    </footer>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
